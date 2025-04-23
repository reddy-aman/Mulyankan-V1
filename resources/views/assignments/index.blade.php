<x-app-layout>
    <div class="flex h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-white shadow-md px-4 py-6">
            <!-- Sidebar content -->
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col p-6 bg-gray-50">
            @php
                $lastOpenedCourse = session('last_opened_course');
                $stages = [
                    'Edit Outline',
                    'Submission Uploaded',
                    'Submission Graded',
                    'Grade Reviewed',
                ];
            @endphp

            <!-- Top Bar -->
            <div class="bg-white shadow-md px-6 py-4 rounded-lg flex justify-between items-center">
                <h1 class="text-2xl font-bold text-gray-800">Assignments</h1>
            </div>

            <!-- Content Area -->
            <div class="flex-1 overflow-auto mt-6">
                @if ($assignments->isEmpty())
                    <div class="p-6 bg-white rounded-lg shadow">
                        <h2 class="text-xl font-semibold text-gray-800 mb-2">No Assignments</h2>
                        <p class="text-gray-600 mb-4">
                            You currently have no assignments. Create an assignment to get started.
                        </p>
                        <a href="{{ route('assignments.create', parameters: $lastOpenedCourse) }}"
                           class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 shadow">
                            + Create Assignment
                        </a>
                    </div>
                @endif

                <div class="bg-white shadow-md rounded-lg overflow-visible mt-4">
                    <table class="min-w-full border border-gray-200 bg-white">
                        <thead class="bg-gray-100 text-gray-700">
                            <tr>
                                <th class="px-4 py-2 border-b text-left">S NO.</th>
                                <th class="px-4 py-2 border-b text-left">Name</th>
                                <th class="px-4 py-2 border-b text-left">Assignment Type</th>
                                <th class="px-4 py-2 border-b text-left">Points</th>
                                <th class="px-4 py-2 border-b text-left">Released</th>
                                <th class="px-4 py-2 border-b text-left">Due (EST)</th>
                                <th class="px-4 py-2 border-b text-left"># Submissions</th>
                                <th class="px-4 py-2 border-b text-left"># Graded</th>
                                <th class="px-4 py-2 border-b text-left">Status</th>
                                <th class="px-4 py-2 border-b text-left">Regrades</th>
                                <th class="px-4 py-2 border-b text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($assignments as $assignment)
                                @php
                                    $currentStage = array_search($assignment->status, $stages);
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2 border-b">{{ $loop->iteration }}</td>
                                    <td class="px-4 py-2 border-b">{{ $assignment->Name }}</td>
                                    <td class="px-4 py-2 border-b">{{ $assignment->type }}</td>
                                    <td class="px-4 py-2 border-b">{{ $assignment->points }}</td>
                                    <td class="px-4 py-2 border-b">
                                        {{ $assignment->release_date ? \Carbon\Carbon::parse($assignment->release_date)->format('M d, Y') : '-' }}
                                    </td>
                                    <td class="px-4 py-2 border-b">
                                        {{ $assignment->due_date ? \Carbon\Carbon::parse($assignment->due_date)->format('M d, Y') : '-' }}
                                    </td>
                                    <td class="px-4 py-2 border-b">{{ $assignment->submissions_count ?? 0 }}</td>
                                    <td class="px-4 py-2 border-b">{{ $assignment->graded_count ?? 0 }}</td>
                                    <td class="px-4 py-2 border-b">
                                    <div class="flex flex-col space-y-1">
                                        @foreach($stages as $index => $stage)
                                            <div class="flex items-center">
                                            {{-- arrow only on current stage --}}
                                            @if($index === $currentStage + 1)
                                                <span class="text-blue-500 mr-2">➤</span>
                                            @else
                                                <span class="w-5"></span>
                                            @endif

                                            {{-- pick the right route for each stage --}}
                                            @php
                                                switch($stage) {
                                                case 'Edit Outline':
                                                    $url = route('assignments.annotateTemplate', ['assignment' => $assignment->id]); break;
                                                case 'Submission Uploaded':
                                                    $url = route('assignments.uploadForm', $assignment->id);   break;
                                                case 'Submission Graded':
                                                    $url = "";   break;
                                                case 'Grade Reviewed':
                                                    $url = "";   break;
                                                }
                                            @endphp

                                            <a href="{{ $url }}"
                                                class="{{ $index === $currentStage + 1
                                                            ? 'text-blue-600 font-semibold'
                                                            : 'text-gray-700 hover:text-blue-600' }}">
                                                {{ $stage }}
                                            </a>
                                            </div>
                                        @endforeach
                                        </div>
                                    </td>
                                    <td class="px-4 py-2 border-b">{{ $assignment->regrades_count ?? 0 }}</td>
                                    <td class="px-4 py-2 border-b text-right">
                                        <div class="relative inline-block">
                                            <!-- Button -->
                                            <button type="button"
                                                    class="action-btn text-gray-500 hover:text-gray-700 focus:outline-none">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    class="h-5 w-5"
                                                    fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path d="M10 3a1.5 1.5 0 110 3 1.5 1.5 0 010-3zm0 5a1.5 1.5 0 110 3 1.5 1.5 0 010-3zm0 5a1.5 1.5 0 110 3 1.5 1.5 0 010-3z"/>
                                                </svg>
                                            </button>

                                                <!-- Dropdown -->
                                            <div class="action-menu hidden absolute right-0 mt-2 w-40 bg-white border border-gray-200 rounded shadow-md z-50 max-h-96 overflow-y-auto">      
                                                <a href="{{ route('assignments.edit', $assignment->id) }}"
                                                    class="block px-4 py-2 whitespace-nowrap text-gray-700 hover:bg-gray-100">
                                                    Edit Assignment
                                                </a>
                                                <form method="POST" action="{{ route('assignments.deleteAssignment', $assignment->id) }}"
                                                        onsubmit="return confirm('Are you sure you want to delete this assignment?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="block w-full text-left px-4 py-2 whitespace-nowrap text-gray-600 hover:bg-gray-100">
                                                    Delete Assignment
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Bottom Buttons -->
            <div class="bg-white shadow-md px-6 py-4 rounded-t-lg flex justify-end space-x-3 mt-6">
                <a href="#"
                   class="px-4 py-2 bg-white text-gray-800 border border-gray-300 rounded shadow-md hover:bg-gray-100">
                    Download Grades
                </a>
                <a href="#"
                   class="px-4 py-2 bg-white text-gray-800 border border-gray-300 rounded shadow-md hover:bg-gray-100">
                    Organize Exam Versions
                </a>
                <a href="#"
                   class="px-4 py-2 bg-white text-gray-800 border border-gray-300 rounded shadow-md hover:bg-gray-100">
                    Duplicate Assignment
                </a>
                <a href="{{ route('assignments.create', parameters: $lastOpenedCourse) }}"
                   class="px-4 py-2 bg-blue-600 text-white border border-blue-600 rounded shadow-md hover:bg-blue-700">
                    + Create Assignment
                </a>
            </div>
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  // Toggle dropdown visibility
  document.querySelectorAll('.action-btn').forEach(btn => {
    btn.addEventListener('click', e => {
      e.stopPropagation();
      const container = btn.closest('.relative');
      const menu = container.querySelector('.action-menu');

      // Hide all other menus
      document.querySelectorAll('.action-menu').forEach(m => {
        if (m !== menu) m.classList.add('hidden');
      });

      // Toggle this one
      menu.classList.toggle('hidden');
    });
  });

  // Close menus when clicking outside
  document.addEventListener('click', () => {
    document.querySelectorAll('.action-menu').forEach(menu => {
      menu.classList.add('hidden');
    });
  });
});
</script>

</x-app-layout>
