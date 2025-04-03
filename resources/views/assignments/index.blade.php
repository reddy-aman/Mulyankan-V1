<x-app-layout>
    <div class="flex h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-white shadow-md px-4 py-6">
            <!-- <h2 class="text-xl font-bold mb-6 text-gray-700">Mulyankan</h2> -->
            <nav>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col p-6 bg-gray-50">
            @php
                $lastOpenedCourse = session()->has('last_opened_course') ? session('last_opened_course') : null;
            @endphp
            <!-- Top Bar -->
            <div class="bg-white shadow-md px-6 py-4 rounded-lg flex justify-between items-center">
                <h1 class="text-2xl font-bold text-gray-800">Assignments</h1>
            </div>

            {{-- <div class="flex flex-col md:flex-row justify-between items-start md:items-center mt-6">
                <div style="padding-left: 1%;">
                    <p class="text-gray-700 mt-1 text-lg flex items-end space-x-2">
                        <strong class="text-2xl text-black font-extrabold">{{ $course->course_number }}</strong>
                        <strong class="text-xl">{{ $course->course_name }}</strong>
                    </p>
                    <span class="text-gray-1000">{{ $course->term }} {{ $course->year }}</span>
                    <p class="mt-4">
                        <span class="font-semibold text-gray-800">Description</span>
                        <br>
                        <span class="text-gray-800">{{ $course->course_description }}</span>
                    </p>
                </div>
                <div class="md:text-right ">
                </div>
            </div> --}}

            <!-- Content Area -->
            <div class="flex-1 overflow-auto mt-6">
                @if($assignments->isEmpty())
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
                <div class="bg-white shadow-md rounded-lg overflow-hidden">
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
                                <th class="px-4 py-2 border-b text-left">Published</th>
                                <th class="px-4 py-2 border-b text-left">Regrades</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($assignments as $assignment)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2 border-b">{{ $loop->iteration }}</td>
                                    <td class="px-4 py-2 border-b">{{ $assignment->Name }}</td>
                                    <td class="px-4 py-2 border-b">{{ $assignment->type }}</td>
                                    <td class="px-4 py-2 border-b">{{ $assignment->points }}</td>
                                    <td class="px-4 py-2 border-b">{{ $assignment->release_date ? \Carbon\Carbon::parse($assignment->release_date)->format('M d, Y') : '-' }}</td>
                                    <td class="px-4 py-2 border-b">{{ $assignment->due_date ? \Carbon\Carbon::parse($assignment->release_date)->format('M d, Y') : '-' }}</td>
                                    <td class="px-4 py-2 border-b">{{ $assignment->submissions_count ?? 0 }}</td>
                                    <td class="px-4 py-2 border-b">{{ $assignment->graded_count ?? 0 }}</td>
                                    <td class="px-4 py-2 border-b">{{ $assignment->status ? 'Yes' : 'No' }}</td>
                                    <td class="px-4 py-2 border-b">{{ $assignment->regrades_count ?? 0 }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Bottom Buttons (Fixed to bottom) -->
            <div class="bg-white shadow-md px-6 py-4 rounded-t-lg flex justify-end space-x-3">
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
    </div>
</x-app-layout>
