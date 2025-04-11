<x-app-layout>
    <div class="flex h-screen">
        <!-- Sidebar (you can call your sidebar partial here if desired) -->
        <aside class="w-64 bg-white shadow-md px-4 py-6">
            <nav>
                <!-- Sidebar content (if any) -->
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col p-6 bg-gray-50" x-data="{
            openModal: false,
            showCSVUpload: false,
            showSingleUser: false,
            showEditModal: false,
            editUserData: { id: '', name: '', email: '', sid: '', role: '' },
            getActionUrl() {
                return this.editUserData.id ? `/courses/${this.editUserData.id}/editUser` : '#';
            },
            updateUser() {
                this.showEditModal = false;
            }
        }"
            x-on:close-modal.window="showCSVUpload = false; showSingleUser = false; showEditModal = false;"
            x-on:user-updated.window="editUserData = $event.detail; showEditModal = false">

            <!-- Main Content Wrapper -->
            <div id="mainContent" class="w-full flex-grow relative">
                <div class="bg-white shadow-md px-6 py-4 rounded-lg flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-800">Course Roster</h1>
                </div>

                <!-- Filtering & Search -->
                <div class="bg-white shadow-md px-6 py-4 rounded-lg mb-6 mt-6 flex items-center">
                    <div class="flex justify-between items-center w-full">
                        <!-- Role Selection Form -->
                        <form method="GET" action="{{ route('courses_student.roster', ['id' => $course->id]) }}"
                            class="flex w-full items-center">
                            <div class="flex-1">
                                <select name="role" class="border border-gray-300 px-4 py-2 rounded shadow-sm"
                                    onchange="this.form.submit()">
                                    <option value="all" {{ request('role', 'all') == 'all' ? 'selected' : '' }}>All
                                    </option>
                                    <option value="students" {{ request('role') == 'students' ? 'selected' : '' }}>
                                        Students</option>
                                    <option value="instructors"
                                        {{ request('role') == 'instructors' ? 'selected' : '' }}>Instructors</option>
                                    <option value="TA" {{ request('role') == 'TA' ? 'selected' : '' }}>TA</option>
                                </select>
                            </div>
                        </form>

                        <!-- Search Form -->
                        <form method="GET" action="{{ route('courses_student.roster', ['id' => $course->id]) }}"
                            class="flex items-center space-x-2 ml-4">
                            <input type="text" name="search" placeholder="Search" value="{{ request('search') }}"
                                class="border border-gray-300 px-4 py-2 rounded shadow-sm">
                            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded shadow-sm">
                                Search
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Table (Roster Data) -->
                <div class="mt-6 bg-white shadow-md rounded-lg overflow-auto" style="max-height: calc(100vh - 300px);">
                    <table class="table-auto w-full border-collapse border border-gray-200">
                        <thead>
                            <tr class="bg-gray-100 text-gray-700">
                                <th class="border border-gray-200 px-6 py-3 text-left">Name</th>
                                <th class="border border-gray-200 px-6 py-3 text-left">Email</th>
                                <th class="border border-gray-200 px-6 py-3 text-left">Role</th>
                                <th class="border border-gray-200 px-6 py-3 text-left">Submissions</th>
                            </tr>
                        </thead>
                        <tbody id="userTableBody">
                            <!-- Iterate over instructors, students, and TAs -->
                            @foreach ($allInstructors as $instructor)
                                <tr id="user-row-{{ $instructor->email }}"
                                    class="roster-item border border-gray-200 bg-white hover:bg-gray-50">
                                    <td class="user-name border border-gray-200 px-6 py-3">{{ $instructor->name }}</td>
                                    <td class="user-email border border-gray-200 px-6 py-3">{{ $instructor->email }}
                                    </td>
                                    <td class="user-role border border-gray-200 px-6 py-3">Instructor</td>
                                    <td class="border border-gray-200 px-6 py-3">0</td>
                                </tr>
                            @endforeach

                            @foreach ($students as $student)
                                <tr id="user-row-{{ $student->email }}"
                                    class="roster-item border border-gray-200 bg-white hover:bg-gray-50">
                                    <td class="user-name border border-gray-200 px-6 py-3">{{ $student->name }}</td>
                                    <td class="user-email border border-gray-200 px-6 py-3">{{ $student->email }}</td>
                                    <td class="user-role border border-gray-200 px-6 py-3">Student</td>
                                    <td class="border border-gray-200 px-6 py-3">0</td>
                                </tr>
                            @endforeach

                            @foreach ($ta as $assistant)
                                <tr id="user-row-{{ $assistant->email }}"
                                    class="roster-item border border-gray-200 bg-white hover:bg-gray-50">
                                    <td class="user-name border border-gray-200 px-6 py-3">{{ $assistant->name }}</td>
                                    <td class="user-email border border-gray-200 px-6 py-3">{{ $assistant->email }}
                                    </td>
                                    <td class="user-role border border-gray-200 px-6 py-3">TA</td>
                                    <td class="border border-gray-200 px-6 py-3">0</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Bottom Bar / Footer Actions -->
                <div
                    class="w-full bg-white shadow-lg py-4 px-6 flex flex-col md:flex-row md:items-center md:justify-between rounded-lg bottom-0 absolute left-0">
                    <span class="text-gray-600">
                        <span id="studentCount" data-count="{{ is_countable($students) ? count($students) : 0 }}">
                            {{ is_countable($students) && count($students) > 0 ? count($students) . (count($students) == 1 ? ' Student' : ' Students') : '0 Students' }}
                        </span>
                        |
                        <span id="instructorCount"
                            data-count="{{ is_countable($allInstructors) ? count($allInstructors) : 0 }}">
                            {{ is_countable($allInstructors) && count($allInstructors) > 0 ? count($allInstructors) . (count($allInstructors) == 1 ? ' Instructor' : ' Instructors') : '0 Instructors' }}
                        </span>
                        |
                        <span id="taCount" data-count="{{ is_countable($ta) ? count($ta) : 0 }}">
                            {{ is_countable($ta) && count($ta) > 0 ? count($ta) . (count($ta) == 1 ? ' TA' : ' TAs') : '0 TAs' }}
                        </span>
                    </span>
                </div>
            </div>

            <!-- Modals -->
            @include('instructor.main-modal')
            @include('instructor.add-user-modal')
            @include('instructor.csv-upload-modal')
            @include('instructor.edit-user-modal')
        </div>
    </div>
</x-app-layout>

<div id="successMessage" class="hidden mt-4 p-2 bg-green-500 text-white rounded"></div>
