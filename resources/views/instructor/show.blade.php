<x-app-layout>
    <div class="container mx-auto p-6 mt-20">

        <!-- Header Row: Course Info & "Things To Do" -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">

            <!-- Left: Course Info -->
            <div class="mb-4 md:mb-0">
                <h1 class="text-2xl font-bold text-blue-800">
                    {{ $course->course_name }}
                    <span class="ml-2 text-gray-600">{{ $course->term }} {{ $course->year }}</span>
                </h1>
                <p class="text-gray-700 mt-1">
                    <strong>Course ID:</strong> {{ $course->course_number }}
                </p>
                <p class="mt-4">
                    <span class="font-semibold text-gray-800">Description</span>
                    <br>
                    <span class="text-gray-600">{{ $course->course_description }}</span>
                </p>
            </div>

            <!-- Right: Things To Do -->
            <div class="md:text-right">
                <p class="text-sm font-semibold text-gray-600 mb-1">Things To Do</p>
                <ul class="list-none space-y-1 text-sm">
                    <li>
                        Add students or staff to your course from the
                        <a href="#" class="text-blue-500 hover:underline">
                            Roster
                        </a>
                        page.
                    </li>
                    <li>
                        Create your first assignment from the
                        <a href="#" class="text-blue-500 hover:underline">
                            Assignments
                        </a>
                        page.
                    </li>
                </ul>
            </div>
        </div>

        <!-- Table Headers -->
        <div class="overflow-x-auto mb-6">
            <table class="min-w-full text-left border-b border-gray-200">
                <thead class="bg-white">
                    <tr class="text-gray-600 border-b">
                        <th class="py-2 px-4 font-semibold">+Active Assignments</th>
                        <th class="py-2 px-4 font-semibold">Released</th>
                        <th class="py-2 px-4 font-semibold">Due (EST)</th>
                        <th class="py-2 px-4 font-semibold">% Submissions</th>
                        <th class="py-2 px-4 font-semibold">% Graded</th>
                        <th class="py-2 px-4 font-semibold">Published</th>
                        <th class="py-2 px-4 font-semibold">Regrades</th>
                    </tr>
                </thead>
            </table>
        </div>

        <!-- No Assignments Section -->
        <div class="text-center py-10 border-t border-gray-200">
            <p class="text-gray-700 mb-1">You currently have no assignments.</p>
            <p class="text-gray-500 mb-4">Create an assignment to get started.</p>
            <button
                class="bg-blue-700 text-white px-4 py-2 rounded-lg hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-300">
                Create Assignment
            </button>
        </div>
    </div>
</x-app-layout>