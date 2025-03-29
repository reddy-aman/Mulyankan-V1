You said:
<x-app-layout>
    <div class="flex h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-white shadow-md px-4 py-6">
            <nav>
                <!-- Sidebar links go here -->
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col p-6 bg-gray-50">
            <!-- Top Bar -->
            <div class="bg-white shadow-md px-6 py-4 rounded-lg flex justify-between items-center">
                <h1 class="text-2xl font-bold text-gray-800">Course Settings</h1>
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

            <!-- Basic Settings -->
            <div class="mt-6 p-6 bg-white rounded-lg shadow">
                <h2 class="text-xl font-semibold text-gray-800 mb-2">Basic Settings</h2>
                <label class="block text-gray-700">Course Number *</label>
                <input type="text" class="block w-full p-2 border border-gray-300 rounded mb-4" placeholder="TS101">

                <label class="block text-gray-700">Course Name *</label>
                <input type="text" class="block w-full p-2 border border-gray-300 rounded mb-4"
                    placeholder="Testing Course">

                <label class="block text-gray-700">Course Description</label>
                <textarea class="block w-full p-2 border border-gray-300 rounded mb-4">This is a testing course created to test the layout</textarea>

                <label class="block text-gray-700">Term</label>
                <select class="block w-full p-2 border border-gray-300 rounded mb-4">
                    <option>Spring</option>
                    <option>Fall</option>
                    <option>Winter</option>
                    <option>Summer</option>
                </select>

                <label class="block text-gray-700">Year</label>
                <select class="block w-full p-2 border border-gray-300 rounded mb-4">
                    <option>2025</option>
                    <option>2024</option>
                    <option>2023</option>
                    <option>2022</option>
                </select>

                <label class="block text-gray-700">Department</label>
                <select class="block w-full p-2 border border-gray-300 rounded mb-4">
                    <option>Computer Science</option>
                    <option>Mathematics</option>
                    <option>Physics</option>
                </select>

                <label class="block text-gray-700">Entry Code</label>
                <input type="checkbox" class="form-checkbox h-5 w-5 text-blue-600">
                <span class="ml-2">Allow students to enroll via course entry code</span>
            </div>

            <!-- Regrade Requests Settings -->
            <div class="mt-6 p-6 bg-white rounded-lg shadow">
                <h2 class="text-xl font-semibold text-gray-800 mb-2">Regrade Requests Settings</h2>
                <label class="inline-flex items-center mt-2">
                    <input type="checkbox" class="form-checkbox h-5 w-5 text-blue-600" checked>
                    <span class="ml-2">Enable Regrade Requests</span>
                </label>
            </div>

            <!-- Grading Defaults -->
            <div class="mt-6 p-6 bg-white rounded-lg shadow">
                <h2 class="text-xl font-semibold text-gray-800 mb-2">Grading Defaults</h2>
                <label class="block text-gray-700">Default Scoring Method</label>
                <label class="inline-flex items-center">
                    <input type="radio" class="form-radio text-blue-600" name="scoring" checked>
                    <span class="ml-2">Negative Scoring</span>
                </label>
                <label class="inline-flex items-center ml-6">
                    <input type="radio" class="form-radio text-blue-600" name="scoring">
                    <span class="ml-2">Positive Scoring</span>
                </label>
                <label class="block text-gray-700 mt-4">Default Score Bounds</label>
                <label class="inline-flex items-center">
                    <input type="checkbox" class="form-checkbox h-5 w-5 text-blue-600" checked>
                    <span class="ml-2">Ceiling (maximum score as determined by the Assignment Outline)</span>
                </label>
                <label class="inline-flex items-center ml-6">
                    <input type="checkbox" class="form-checkbox h-5 w-5 text-blue-600" checked>
                    <span class="ml-2">Floor (minimum score is 0.0)</span>
                </label>
            </div>

            <!-- Modify Course -->
            <div class="mt-6 p-6 bg-white rounded-lg shadow flex justify-between">
                <button class="bg-red-600 text-white px-4 py-2 rounded">Unpublish All Grades</button>
                <div>
                    <button class="bg-gray-400 text-white px-4 py-2 rounded">Duplicate Course</button>
                    <button class="bg-red-600 text-white px-4 py-2 rounded">Delete Course</button>
                    <button class="bg-blue-600 text-white px-4 py-2 rounded">Update Course</button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
