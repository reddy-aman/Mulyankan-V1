<!-- resources/views/instructor/course_setting.blade.php -->
<x-app-layout>
    <div class="flex h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-white shadow-md px-4 py-6">
            <nav>
                <a href="/mulyankan/courses" class="block py-2 px-4 text-gray-700 hover:bg-gray-100 rounded">Back to
                    Courses</a>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col p-6 bg-gray-50">
            <!-- Top Bar -->
            <div class="bg-white shadow-md px-6 py-4 rounded-lg flex justify-between items-center">
                <h1 class="text-2xl font-bold text-gray-800">Course Settings</h1>
            </div>

            <!-- Success Message -->
            @if (session('success'))
                <div class="mt-4 p-4 bg-green-100 text-green-700 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Basic Settings -->
            <form id="update-course-form" action="{{ route('courses.updateSettings', $course->id) }}" method="POST"
                class="mt-6 p-6 bg-white rounded-lg shadow">
                @csrf
                @method('PUT')

                <h2 class="text-xl font-semibold text-gray-800 mb-4">Basic Settings</h2>

                <!-- Course Number -->
                <div class="mb-4">
                    <label for="course_number" class="block text-gray-700 font-medium">Course Number *</label>
                    <input type="text" id="course_number" name="course_number"
                        value="{{ old('course_number', $course->course_number) }}"
                        class="block w-full p-2 border border-gray-300 rounded @error('course_number') border-red-500 @enderror"
                        placeholder="TS101" required>
                    @error('course_number')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Course Name -->
                <div class="mb-4">
                    <label for="course_name" class="block text-gray-700 font-medium">Course Name *</label>
                    <input type="text" id="course_name" name="course_name"
                        value="{{ old('course_name', $course->course_name) }}"
                        class="block w-full p-2 border border-gray-300 rounded @error('course_name') border-red-500 @enderror"
                        placeholder="Testing Course" required>
                    @error('course_name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Course Description -->
                <div class="mb-4">
                    <label for="course_description" class="block text-gray-700 font-medium">Course Description</label>
                    <textarea id="course_description" name="course_description"
                        class="block w-full p-2 border border-gray-300 rounded @error('course_description') border-red-500 @enderror"
                        rows="4" placeholder="This is a testing course created to test the layout">{{ old('course_description', $course->course_description) }}</textarea>
                    @error('course_description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Term -->
                <div class="mb-4">
                    <label for="term" class="block text-gray-700 font-medium">Term</label>
                    <select id="term" name="term"
                        class="block w-full p-2 border border-gray-300 rounded @error('term') border-red-500 @enderror"
                        required>
                        @foreach (['Spring', 'Fall', 'Winter', 'Summer'] as $term)
                            <option value="{{ $term }}"
                                {{ old('term', $course->term) == $term ? 'selected' : '' }}>{{ $term }}
                            </option>
                        @endforeach
                    </select>
                    @error('term')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Year -->
                <div class="mb-4">
                    <label for="year" class="block text-gray-700 font-medium">Year</label>
                    <select id="year" name="year"
                        class="block w-full p-2 border border-gray-300 rounded @error('year') border-red-500 @enderror"
                        required>
                        @foreach ([2025, 2024, 2023, 2022] as $year)
                            <option value="{{ $year }}"
                                {{ old('year', $course->year) == $year ? 'selected' : '' }}>{{ $year }}
                            </option>
                        @endforeach
                    </select>
                    @error('year')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Department -->
                <div class="mb-4">
                    <label for="department" class="block text-gray-700 font-medium">Department</label>
                    <select id="department" name="department"
                        class="block w-full p-2 border border-gray-300 rounded @error('department') border-red-500 @enderror"
                        required>
                        @foreach (['Computer Science', 'Mathematics', 'Physics'] as $dept)
                            <option value="{{ $dept }}"
                                {{ old('department', $course->department) == $dept ? 'selected' : '' }}>
                                {{ $dept }}</option>
                        @endforeach
                    </select>
                    @error('department')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Modify Course -->
                {{-- <div class="mt-6 flex justify-end space-x-4">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        Update Course
                    </button>
                </div> --}}


            </form>
            {{-- <form action="{{ route('courses.destroy', $course->id) }}" method="POST"
                onsubmit="return confirm('Are you sure you want to delete this course?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                    Delete Course
                </button>
            </form> --}}


            <div class="mt-6 flex justify-end space-x-4">
                <!-- Update button outside the form, targeting it by ID -->
                <button type="submit" form="update-course-form"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Update Course
                </button>

                <!-- Delete form as a sibling -->
                <form action="{{ route('courses.destroy', $course->id) }}" method="POST"
                    onsubmit="return confirm('Are you sure you want to delete this course?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                        Delete Course
                    </button>
                </form>
            </div>

        </div>


    </div>
</x-app-layout>
