<x-app-layout>
    @if(auth()->user()->getRoleNames()->contains('Instructor'))
        @php
            $currentRoute = Route::currentRouteName();
            $lastOpenedCourse = session()->has('last_opened_course') ? session('last_opened_course') : null;
        @endphp
    @endif

    <div class="flex h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-white shadow-md px-4 py-6">
            <h2 class="text-xl font-bold mb-6 text-gray-700">Mulyankan</h2>
            <nav>
                <a href="{{ route('courses.show', $lastOpenedCourse) }}" class="block px-4 py-2 text-gray-700 rounded hover:bg-gray-100">Dashboard</a>
                <a href="{{ route('instructor.create-courses') }}" class="block px-4 py-2 text-gray-700 rounded hover:bg-gray-100">Courses</a>
                <a href="{{ route('assignments.index', $lastOpenedCourse) }}" class="block px-4 py-2 text-gray-700 rounded hover:bg-gray-100 font-semibold">Assignments</a>
                <a href="{{ route('courses.roster', $lastOpenedCourse) }}" class="block px-4 py-2 text-gray-700 rounded hover:bg-gray-100">Roster</a>
                <a href="#" class="block px-4 py-2 text-gray-700 rounded hover:bg-gray-100">Extensions</a>
                <a href="#" class="block px-4 py-2 text-gray-700 rounded hover:bg-gray-100">Course Settings</a>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col p-6 bg-gray-50">
            <!-- Top Bar -->
            <div class="bg-white shadow-md px-6 py-4 rounded-lg flex justify-between items-center">
                <h1 class="text-2xl font-bold text-gray-800">
                    Dashboard
                </h1>
            </div>

            <!-- Course Information & Things To Do -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mt-6">
                <div>
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
                    <p class="text-sm font-semibold text-gray-800 mb-1">Things To Do</p>
                    <ul class="list-none space-y-1 text-sm">
                        <li>
                            Add students or staff to your course from the
                            <a href="{{ route('courses.roster', $lastOpenedCourse) }}" class="text-blue-500 hover:underline">Roster</a> page.
                        </li>
                        <li>
                            Create your first assignment from the
                            <a href="{{ route('assignments.index', $lastOpenedCourse) }}" class="text-blue-500 hover:underline">Assignments</a> page.
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Assignments Table -->
            <div class="mt-6 bg-white shadow-md rounded-lg overflow-hidden">
                <table class="min-w-full border border-gray-200 bg-white">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="px-4 py-2 border-b text-left">+Active Assignments</th>
                            <th class="px-4 py-2 border-b text-left">Released</th>
                            <th class="px-4 py-2 border-b text-left">Due (EST)</th>
                            <th class="px-4 py-2 border-b text-left">% Submissions</th>
                            <th class="px-4 py-2 border-b text-left">% Graded</th>
                            <th class="px-4 py-2 border-b text-left">Published</th>
                            <th class="px-4 py-2 border-b text-left">Regrades</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="7" class="px-4 py-6 text-center text-gray-500">
                                <p class="text-lg">You currently have no assignments.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- No Assignments Section -->
            <div class="mt-6 p-6 bg-white rounded-lg shadow text-center">
                <h2 class="text-xl font-semibold text-gray-800 mb-2">No Assignments</h2>
                <p class="text-gray-600 mb-4">You currently have no assignments. Create an assignment to get started.</p>
                <a href="{{ route('assignments.index', $lastOpenedCourse) }}" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 shadow">
                    + Create Assignment
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
