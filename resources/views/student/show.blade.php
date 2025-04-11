<x-app-layout>
    @if (auth()->user()->getRoleNames()->contains('Student'))
        @php
            $currentRoute = Route::currentRouteName();
            $lastOpenedCourse = session()->has('last_opened_course') ? session('last_opened_course') : null;
            $stages = ['Assignment Created', 'Submission Uploaded', 'Submission Graded', 'Grade Reviewed'];
        @endphp
    @endif

    <div class="flex h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-white shadow-md px-4 py-6">
            {{-- sidebar  --}}
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col p-6 bg-gray-50">
            <!-- Top Bar -->
            <div class="bg-white shadow-md px-6 py-4 rounded-lg flex justify-between items-center">
                <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
            </div>

            <!-- Course Information -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mt-6">
                <div class="pl-4">
                    <p class="text-gray-700 mt-1 text-lg flex items-end space-x-2">
                        <strong class="text-2xl text-black font-extrabold">{{ $course->course_number }}</strong>
                        <strong class="text-xl">{{ $course->course_name }}</strong>
                    </p>
                    <span class="text-gray-900">{{ $course->term }} {{ $course->year }}</span>
                    <p class="mt-4">
                        <span class="font-semibold text-gray-800">Description</span>
                        <br>
                        <span class="text-gray-800">{{ $course->course_description }}</span>
                    </p>
                </div>
            </div>

            <!-- Assignments Section -->
            <div class="flex-1 overflow-auto mt-6">
                @if ($assignments->isEmpty())
                    <div class="p-6 bg-white rounded-lg shadow">
                        <h2 class="text-xl font-semibold text-gray-800 mb-2">No Assignments</h2>
                        <p class="text-gray-600 mb-4">
                            There are currently no assignments for this course.
                        </p>
                    </div>
                @else
                    <div class="bg-white shadow-md rounded-lg overflow-hidden mt-4">
                        <table class="min-w-full border border-gray-200 bg-white">
                            <thead class="bg-gray-100 text-gray-700">
                                <tr>
                                    <th class="px-4 py-2 border-b text-left">S NO.</th>
                                    <th class="px-4 py-2 border-b text-left">Assignment Name</th>
                                    <th class="px-4 py-2 border-b text-left">Points</th>
                                    <th class="px-4 py-2 border-b text-left">Released</th>
                                    <th class="px-4 py-2 border-b text-left">Due (EST)</th>
                                    <th class="px-4 py-2 border-b text-left">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($assignments as $assignment)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-2 border-b">{{ $loop->iteration }}</td>
                                        <td class="px-4 py-2 border-b">
                                            <!-- Replace route name with the proper student route to view assignment details -->
                                            <a href="#" class="text-blue-600 hover:underline">
                                                {{ $assignment->Name }}
                                            </a>
                                        </td>
                                        <td class="px-4 py-2 border-b">{{ $assignment->points }}</td>
                                        <td class="px-4 py-2 border-b">
                                            {{ $assignment->release_date ? \Carbon\Carbon::parse($assignment->release_date)->format('M d, Y') : '-' }}
                                        </td>
                                        <td class="px-4 py-2 border-b">
                                            {{ $assignment->due_date ? \Carbon\Carbon::parse($assignment->due_date)->format('M d, Y') : '-' }}
                                        </td>
                                        <td class="px-4 py-2 border-b">{{ $assignment->status }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
