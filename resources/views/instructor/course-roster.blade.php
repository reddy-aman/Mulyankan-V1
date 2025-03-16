<x-app-layout>
    <!-- Top-level container with Alpine data and listener for close-modal event -->
    <div class="flex flex-col min-h-screen pt-20" x-data="{
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
        }" x-on:close-modal.window="
            showCSVUpload = false;
            showSingleUser = false;
            showEditModal = false;" x-on:user-updated.window="editUserData = $event.detail; showEditModal = false">
        <!-- Main Content -->
        <div id="mainContent" class="container mx-auto pt-10 p-6 flex-grow">
            <h2 class="text-2xl font-bold mb-6">Course Roster</h2>

            <!-- Dropdown for filtering -->
            <div class="mb-6 flex justify-between items-center">
                <form method="GET" action="{{ route('courses.roster', ['id' => $course->id]) }}" class="flex w-full">
                    <!-- Left Side: Role Dropdown -->
                    <div class="flex-1">
                        <select name="role" class="border border-gray-300 px-4 py-2 rounded shadow-sm"
                            onchange="this.form.submit()">
                            <option value="all" {{ request('role', 'all') == 'all' ? 'selected' : '' }}>All</option>
                            <option value="students" {{ request('role') == 'students' ? 'selected' : '' }}>Students
                            </option>
                            <option value="instructors" {{ request('role') == 'instructors' ? 'selected' : '' }}>
                                Instructors</option>
                            <option value="TA" {{ request('role') == 'TA' ? 'selected' : '' }}>TA</option>
                        </select>
                    </div>
                </form>

                <!-- Right Side: Search Input -->
                <div class="flex items-center space-x-2">
                    <input type="text" name="search" placeholder="Search" value="{{ request('search') }}"
                        class="border border-gray-300 px-4 py-2 rounded shadow-sm w-full">
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded shadow-sm">Search</button>
                </div>
            </div>


            <!-- Table -->
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <table class="table-auto w-full border-collapse border border-gray-200">
                    <thead>
                        <tr class="bg-gray-100 text-gray-700">
                            <th class="border border-gray-200 px-6 py-3 text-left">Name</th>
                            <th class="border border-gray-200 px-6 py-3 text-left">Email</th>
                            <th class="border border-gray-200 px-6 py-3 text-left">Role</th>
                            <th class="border border-gray-200 px-6 py-3 text-left">Submissions</th>
                            <th class="border border-gray-200 px-6 py-3 text-left">Edit</th>
                            <th class="border border-gray-200 px-6 py-3 text-left">Remove</th>
                        </tr>
                    </thead>
                    <tbody id="userTableBody">
                        @foreach ($allInstructors as $instructor)
                            <tr id="user-row-{{ $instructor->id }}"
                                class="roster-item border border-gray-200 bg-white hover:bg-gray-50">
                                <td class="user-name border border-gray-200 px-6 py-3">{{ $instructor->name }}</td>
                                <td class="user-email border border-gray-200 px-6 py-3">{{ $instructor->email }}</td>
                                <td class="user-role border border-gray-200 px-6 py-3">Instructor</td>
                                <td class="border border-gray-200 px-6 py-3">0</td>
                                <td class="border border-gray-200 px-6 py-3 text-center">
                                    @if ($instructor->email != $pri_instructor_email)
                                        <i class="fa fa-pencil text-gray-500 cursor-pointer hover:text-gray-700" title="Edit"
                                            data-id="{{ $instructor->id }}" data-name="{{ $instructor->name }}"
                                            data-email="{{ $instructor->email }}" data-sid="{{ $instructor->sid ?? '' }}"
                                            data-role="2"
                                            @click="
                                                                                                                                                                            showEditModal = true;
                                                                                                                                                                            editUserData.id = $event.target.dataset.id;
                                                                                                                                                                            editUserData.name = $event.target.dataset.name;
                                                                                                                                                                            editUserData.email = $event.target.dataset.email;
                                                                                                                                                                            editUserData.sid = $event.target.dataset.sid;
                                                                                                                                                                            editUserData.role = $event.target.dataset.role;
                                                                                                                                                                        ">
                                        </i>
                                    @endif

                                </td>
                                <td class="border border-gray-200 px-6 py-3 text-center">
                                    @if ($instructor->email != $pri_instructor_email)
                                        <i class="fa fa-times text-red-500 cursor-pointer hover:text-red-700 delete-user-button"
                                            data-user-id="{{ $instructor->id }}" title="Remove">
                                        </i>
                                    @endif
                                </td>
                            </tr>
                        @endforeach

                        @foreach ($students as $student)
                            <tr id="user-row-{{ $student->id }}"
                                class="roster-item border border-gray-200 bg-white hover:bg-gray-50">
                                <td class="user-name border border-gray-200 px-6 py-3">{{ $student->name }}</td>
                                <td class="user-email border border-gray-200 px-6 py-3">{{ $student->email }}</td>
                                <td class="user-role border border-gray-200 px-6 py-3">Student</td>
                                <td class="border border-gray-200 px-6 py-3">0</td>
                                <td class="border border-gray-200 px-6 py-3 text-center">
                                    <i class="fa fa-pencil text-gray-500 cursor-pointer hover:text-gray-700" title="Edit"
                                        data-id="{{ $student->id }}" data-name="{{ $student->name }}"
                                        data-email="{{ $student->email }}" data-sid="{{ $student->sid ?? '' }}"
                                        data-role="1" @click="
                                                                                     showEditModal = true;
                                                                                     editUserData.id = $event.target.dataset.id;
                                                                                     editUserData.name = $event.target.dataset.name;
                                                                                     editUserData.email = $event.target.dataset.email;
                                                                                     editUserData.sid = $event.target.dataset.sid;
                                                                                     editUserData.role = $event.target.dataset.role;
                                                                                ">
                                    </i>

                                </td>
                                <td class="border border-gray-200 px-6 py-3 text-center">
                                    <i class="fa fa-times text-red-500 cursor-pointer hover:text-red-700 delete-user-button"
                                        data-user-id="{{ $student->id }}" title="Remove">
                                    </i>
                                </td>
                            </tr>
                        @endforeach

                        @foreach ($ta as $assistant)
                            <tr id="user-row-{{ $assistant->id }}"
                                class="roster-item border border-gray-200 bg-white hover:bg-gray-50">
                                <td class="user-name border border-gray-200 px-6 py-3">{{ $assistant->name }}</td>
                                <td class="user-email border border-gray-200 px-6 py-3">{{ $assistant->email }}</td>
                                <td class="user-role border border-gray-200 px-6 py-3">TA</td>
                                <td class="border border-gray-200 px-6 py-3">0</td>
                                <td class="border border-gray-200 px-6 py-3 text-center">
                                    <i class="fa fa-pencil text-gray-500 cursor-pointer hover:text-gray-700" title="Edit"
                                        data-id="{{ $assistant->id }}" data-name="{{ $assistant->name }}"
                                        data-email="{{ $assistant->email }}" data-sid="{{ $assistant->sid ?? '' }}"
                                        data-role="3" @click="
                                                                                 showEditModal = true;
                                                                                 editUserData.id = $event.target.dataset.id;
                                                                                 editUserData.name = $event.target.dataset.name;
                                                                                 editUserData.email = $event.target.dataset.email;
                                                                                 editUserData.sid = $event.target.dataset.sid;
                                                                                 editUserData.role = $event.target.dataset.role;
                                                                            ">
                                    </i>

                                </td>
                                <td class="border border-gray-200 px-6 py-3 text-center">
                                    <i class="fa fa-times text-red-500 cursor-pointer hover:text-red-700 delete-user-button"
                                        data-user-id="{{ $assistant->id }}" title="Remove">
                                    </i>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Show "Add Members" message after instructor rows --}}
            <!-- @if($allInstructors->count() <= 1 && count($students) == 0 && count($ta) == 0)
                <tr>
                    <td colspan="3" class="pt-8 pb-6">
                        <div class="flex flex-col items-center justify-center space-y-4">
                            <p class="text-gray-500 text-lg text-center">You haven't added anyone to your course yet.</p>
                            <button class="px-6 py-2 bg-gray-200 border rounded shadow-sm hover:bg-gray-300"
                                @click="openModal = true">
                                Add Members
                            </button>
                        </div>
                    </td>
                </tr>
            @endif -->

            <!-- Footer actions with count updates -->
            <div class="w-full fixed bottom-0 left-0 bg-gray-100 shadow-inner py-6">
                <div class="container mx-auto flex flex-col items-center">
                    <span class="text-gray-600 mb-4">
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
                    <div class="flex gap-3">
                        <button class="px-4 py-2 border rounded bg-gray-100 shadow-sm hover:bg-gray-200">
                            Sync LMS Roster
                        </button>

                        @php
                            $course_id = session('last_opened_course');
                        @endphp
                        <form action="{{ route('courses.rosterDownload', ['id' => $course_id]) }}" method="GET">
                            <button type="submit"
                                class="px-4 py-2 border rounded bg-gray-100 shadow-sm hover:bg-gray-200">
                                Download Roster
                            </button>
                        </form>

                        <button class="px-4 py-2 border rounded bg-gray-100 shadow-sm hover:bg-gray-200">
                            Send Enrollment Notification
                        </button>
                        <button class="px-4 py-2 border rounded bg-blue-500 text-white shadow-sm hover:bg-blue-600"
                            @click="openModal = true">
                            + Add Students or Staff
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Modal -->
        <div x-show="openModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50"
            @click="openModal = false">
            <div class="bg-white p-6 rounded-lg shadow-lg max-w-lg w-full">
                <h2 class="text-xl font-semibold">Add Students or Staff</h2>
                <div class="mt-3 bg-blue-100 p-3 text-sm text-gray-700 rounded flex items-start">
                    <i class="fa fa-info-circle mr-2 text-blue-500"></i>
                    <span>Add a single user or upload a CSV file to add multiple users.</span>
                </div>
                <div class="mt-6 flex justify-around">
                    <!-- Single User Option -->
                    <div class="text-center cursor-pointer hover:opacity-75"
                        @click="openModal = false; showSingleUser = true;">
                        <i class="fa fa-user fa-3x text-blue-500"></i>
                        <p class="text-blue-500 font-semibold mt-2">Single User</p>
                    </div>
                    <!-- CSV File Option -->
                    <div class="text-center cursor-pointer hover:opacity-75"
                        @click="openModal = false; showCSVUpload = true;">
                        <i class="fa fa-users fa-3x text-gray-700"></i>
                        <p class="text-gray-700 font-semibold mt-2">CSV File</p>
                    </div>
                </div>
                <!-- Cancel Button -->
                <div class="mt-6 text-right">
                    <button class="px-4 py-2 bg-gray-200 border rounded hover:bg-gray-300" @click="openModal = false">
                        Cancel
                    </button>
                </div>
            </div>
        </div>

        <!-- Add Single User Modal -->
        <div x-show="showSingleUser" x-cloak
            class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50"
            @click="showSingleUser = false">
            <div class="bg-white p-6 rounded-lg shadow-lg max-w-lg w-full" @click.stop>
                <h2 class="text-2xl font-semibold mb-2">Add a User</h2>
                <!-- Info Banner -->
                <div class="mt-3 bg-blue-100 p-3 text-sm text-gray-700 rounded flex items-start">
                    <i class="fa fa-info-circle mr-2 text-blue-500"></i>
                    <span>Add a single student or staff member.</span>
                </div>
                <!-- Form -->
                <form id="addUserForm" class="mt-4">
                    @csrf
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" placeholder="e.g. Johnny Smith"
                        class="border px-4 py-2 w-full rounded" required>
                    <label class="block text-sm font-medium text-gray-700 mt-4 mb-1">
                        Email Address <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="email" id="email" placeholder="e.g. email@example.com"
                        class="border px-4 py-2 w-full rounded" required>
                    <label class="block text-sm font-medium text-gray-700 mt-4 mb-1">
                        Student ID # (Optional)
                    </label>
                    <input type="text" name="sid" id="sid" placeholder="e.g. 12345"
                        class="border px-4 py-2 w-full rounded">
                    <label class="block text-sm font-medium text-gray-700 mt-4 mb-1">
                        Sections
                    </label>
                    <select class="border px-4 py-2 w-full rounded">
                        <option>Select Sections...</option>
                    </select>
                    <label class="block text-sm font-medium text-gray-700 mt-4 mb-1">
                        Role
                    </label>
                    <div class="flex flex-wrap gap-4 mt-2">
                        <label class="flex items-center space-x-2">
                            <input type="radio" name="role" value="1" checked>
                            <span>Student</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="radio" name="role" value="2">
                            <span>Instructor</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="radio" name="role" value="3">
                            <span>TA</span>
                        </label>
                    </div>
                    <!-- Email Notification -->
                    <div class="mt-4 flex items-center">
                        <input type="checkbox" name="notify_user" value="1" class="mr-2">
                        <span class="text-sm text-gray-700">Let this user know that they were added to the course</span>
                    </div>
                    <!-- Buttons -->
                    <div class="mt-6 flex justify-end space-x-2">
                        <button type="button" class="px-4 py-2 bg-gray-200 border rounded hover:bg-gray-300"
                            @click="showSingleUser = false">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                            Submit
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- CSV Upload Modal -->
        <div x-show="showCSVUpload" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50"
            @click="showCSVUpload = false">
            <div class="bg-white p-6 rounded-lg shadow-lg max-w-lg w-full" @click.stop>
                <h2 class="text-xl font-semibold">Upload CSV</h2>
                <div class="mt-3 bg-blue-100 p-3 text-sm text-gray-700 rounded flex items-start">
                    <i class="fa fa-info-circle mr-2 text-blue-500"></i>
                    <span>Select a CSV file to upload and add multiple users at once.</span>
                </div>
                <form id="csvUploadForm" class="mt-4">
                    @csrf
                    <input type="file" name="csv_file" accept=".csv"
                        class="border p-2 rounded w-full file:mr-4 file:py-2 file:px-4 file:border-0 file:bg-gray-100 file:hover:bg-gray-200"
                        required>
                    <p class="mt-4 text-lg font-semibold text-red-600 bg-red-100 p-3 rounded-lg border border-red-400">
                        ⚠ CSV Format: <strong>Name, Email, Student ID, Role</strong> (Role must be <em>Student,
                            Instructor, or TA</em>)
                    </p>
                    <div class="mt-6 flex justify-end space-x-2">
                        <button type="button" class="px-4 py-2 bg-gray-200 border rounded hover:bg-gray-300"
                            @click="showCSVUpload = false">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                            Upload
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Course Member Modal -->
        <div x-show="showEditModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50"
            @click="showEditModal = false">
            <div class="bg-white p-6 rounded-lg shadow-lg max-w-lg w-full" @click.stop>
                <h2 class="text-2xl font-semibold">Edit Course Member</h2>
                <div class="mt-3 bg-blue-100 p-3 text-sm text-gray-700 rounded flex items-start">
                    <i class="fa fa-info-circle mr-2 text-blue-500"></i>
                    <span>Edit roster information for this course member.</span>
                </div>
                <!-- Form -->
                <form id="editUserForm" x-on:submit.prevent="updateUser" class="mt-4">
                    @csrf
                    <!-- Hidden field to store the user id -->
                    <input type="hidden" name="id" x-model="editUserData.id">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Full Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" x-model="editUserData.name" class="border px-4 py-2 w-full rounded"
                        required>

                    <label class="block text-sm font-medium text-gray-700 mt-4 mb-1">
                        Email Address <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="email" x-model="editUserData.email"
                        class="border px-4 py-2 w-full rounded" required>

                    <label class="block text-sm font-medium text-gray-700 mt-4 mb-1">
                        Role
                    </label>
                    <select name="role" x-model="editUserData.role" class="border px-4 py-2 w-full rounded">
                        <option value="1">Student</option>
                        <option value="2">Instructor</option>
                        <option value="3">TA</option>
                    </select>

                    <label class="block text-sm font-medium text-gray-700 mt-4 mb-1">
                        Student ID
                    </label>
                    <input type="text" name="sid" x-model="editUserData.sid" class="border px-4 py-2 w-full rounded">

                    <div class="mt-4 flex justify-between">
                        <button type="button" class="px-4 py-2 bg-gray-400 text-white rounded hover:bg-gray-500"
                            @click="showEditModal = false">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

<!-- Success Message Element -->
<div id="successMessage" class="hidden mt-4 p-2 bg-green-500 text-white rounded"></div>

<!-- jQuery for AJAX -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>
    var priInstructorEmail = "{{ $pri_instructor_email }}";

    // Global function to update a role counter.
    // selector: the jQuery selector for the counter element
    // singular: label for count 1 (e.g., "Student")
    // plural: label for count other than 1 (e.g., "Students")
    // delta: change in count (e.g., +1 or -1)
    function updateCounter(selector, singular, plural, delta) {
        var $span = $(selector);
        // Try to get the current count from data attribute, if not, parse the displayed text
        var count = parseInt($span.data('count')) || parseInt($span.text()) || 0;
        count = Math.max(0, count + delta);
        $span.data('count', count);
        $span.text(count + (count === 1 ? ' ' + singular : ' ' + plural));
    }

    $(document).ready(function () {
        $('#addUserForm').on('submit', function (e) {
            e.preventDefault(); // Prevent page reload

            $.ajax({
                url: "{{ route('courses.addUser') }}",
                type: "POST",
                data: $(this).serialize(),
                success: function (response) {
                    if (response.success) {
                        // Display the custom success message from the controller
                        $("#successMessage").text(response.message)
                            .removeClass("hidden")
                            .prependTo("#mainContent")
                            .fadeIn()
                            .delay(3000)
                            .fadeOut();


                        // Grab form values
                        var name = $('#name').val();
                        var email = $('#email').val();
                        var roleVal = $('input[name=role]:checked').val();
                        var roleText = (roleVal == 1) ? 'Student' :
                            (roleVal == 2) ? 'Instructor' : 'TA';

                        // Build new row HTML for the table
                        var newRow = `<tr id="user-row-${response.userId}" class="border border-gray-200 bg-white hover:bg-gray-50">
                        <td class="border border-gray-200 px-6 py-3">${name}</td>
                        <td class="border border-gray-200 px-6 py-3">${email}</td>
                        <td class="border border-gray-200 px-6 py-3">
                            ${roleText}
                        </td>
                        <td class="border border-gray-200 px-6 py-3">0</td>
                        <td class="border border-gray-200 px-6 py-3 text-center">
                            <i class="fa fa-pencil text-gray-500 cursor-pointer hover:text-gray-700"></i>
                        </td>
                        <td class="border border-gray-200 px-6 py-3 text-center">
                            <i class="fa fa-times text-red-500 cursor-pointer hover:text-red-700"></i>
                        </td>
                    </tr>`;

                        // Append the new row to the table body
                        $('#userTableBody').append(newRow);

                        // Update footer counts based on role
                        if (roleText === 'Student') {
                            updateCounter('#studentCount', 'Student', 'Students', 1);
                        } else if (roleText === 'Instructor') {
                            updateCounter('#instructorCount', 'Instructor', 'Instructors', 1);
                        } else if (roleText === 'TA') {
                            updateCounter('#taCount', 'TA', 'TAs', 1);
                        }

                        // Reset the form fields
                        $('#addUserForm')[0].reset();

                        // Dispatch a custom event to close the modal
                        window.dispatchEvent(new CustomEvent('close-modal'));
                    }
                },
                error: function (xhr) {
                    alert("Error: " + xhr.responseJSON.message);
                }
            });
        });

        $('#csvUploadForm').on('submit', function (e) {
            e.preventDefault(); // Prevent page reload
            let formData = new FormData(this);

            $.ajax({
                url: "{{ route('courses.uploadCSV') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" }, // Ensure CSRF token is included
                success: function (response) {
                    if (response.success) {
                        alert(response.message);
                        $('#csvUploadForm')[0].reset(); // Clear the form
                        window.dispatchEvent(new CustomEvent('close-modal'));
                        if (response.redirect)
                            window.location.href = response.redirect;
                    } else {
                        alert("Some errors occurred: " + response.errors.join("\n"));
                    }
                },
                error: function (xhr) {
                    let errorMessage = "Failed to upload CSV!";
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                        errorMessage = Object.values(xhr.responseJSON.errors).flat().join(", ");
                    }
                    alert(errorMessage);
                }
            });
        });

        $('#editUserForm').on('submit', function (e) {
            e.preventDefault(); // Prevent page reload

            let formData = new FormData(this);
            // Get the user id from a data attribute on the form
            let userId = $(this).find('input[name="id"]').val();

            $.ajax({
                url: "{{ route('courses.editUser', ['id' => '__ID__']) }}".replace('__ID__', userId),
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                success: function (response) {
                    if (response.success) {
                        let m = response.member;
                        let $rows = $("tr#user-row-" + m.id);
                        // Then pick the row whose email does NOT match the primary instructor's email.
                        let $row = $rows.filter(function () {
                            return $(this).find(".user-email").text().trim() !== priInstructorEmail;
                        }).first();
                        // Fallback: if none match (or for safety), use the first one.
                        if ($row.length === 0) {
                            $row = $rows.first();
                        }
                        var oldRoleText = $row.find(".user-role").text().trim();
                        var newRoleText = m.role == 1 ? "Student" : m.role == 2 ? "Instructor" : "TA";

                        // If the role has changed, update counters accordingly
                        if (oldRoleText !== newRoleText) {
                            // Decrement the counter for the old role
                            if (oldRoleText === "Student") {
                                updateCounter('#studentCount', 'Student', 'Students', -1);
                            } else if (oldRoleText === "Instructor") {
                                updateCounter('#instructorCount', 'Instructor', 'Instructors', -1);
                            } else if (oldRoleText === "TA") {
                                updateCounter('#taCount', 'TA', 'TAs', -1);
                            }
                            // Increment the counter for the new role
                            if (newRoleText === "Student") {
                                updateCounter('#studentCount', 'Student', 'Students', 1);
                            } else if (newRoleText === "Instructor") {
                                updateCounter('#instructorCount', 'Instructor', 'Instructors', 1);
                            } else if (newRoleText === "TA") {
                                updateCounter('#taCount', 'TA', 'TAs', 1);
                            }
                        }
                        // Now update the chosen row
                        $row.find(".user-name").text(m.name);
                        $row.find(".user-email").text(m.email);
                        $row.find(".user-role").text(m.role == 1 ? "Student" : m.role == 2 ? "Instructor" : "TA");

                        $row.find(".fa-pencil")
                            .attr("data-name", m.name)
                            .attr("data-email", m.email)
                            .attr("data-sid", m.sid ?? "")
                            .attr("data-role", m.role);

                        // Dispatch a custom event with the updated member data
                        window.dispatchEvent(new CustomEvent('user-updated', { detail: m }));
                    } else {
                        alert("Error: " + response.message);
                    }
                },
                error: function (xhr) {
                    let errorMessage = "Failed to update user!";
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                        errorMessage = Object.values(xhr.responseJSON.errors).flat().join(", ");
                    }
                    alert(errorMessage);
                }
            });
        });

        $('.delete-user-button').on('click', function (e) {
            e.preventDefault(); // Prevent any default action

            if (!confirm("Are you sure you want to delete this user?")) {
                return;
            }
            let userId = $(this).data('user-id');
            let $userRow = $("#user-row-" + userId);
            // Get the role from the deleted row's cell
            let roleText = $userRow.find('.user-role').text().trim();

            $.ajax({
                url: "{{ route('courses.deleteUser', ['id' => '__ID__']) }}".replace('__ID__', userId),
                type: "DELETE",
                headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                success: function (response) {
                    if (response.success) {
                        $userRow.remove();

                        // Function to update counter given the span selector and singular/plural text
                        if (roleText === 'Student') {
                            updateCounter('#studentCount', 'Student', 'Students', -1);
                        } else if (roleText === 'Instructor') {
                            updateCounter('#instructorCount', 'Instructor', 'Instructors', -1);
                        } else if (roleText === 'TA') {
                            updateCounter('#taCount', 'TA', 'TAs', -1);
                        }
                        // Optionally display a success message
                    } else {
                        alert("Error: " + response.message);
                    }
                },
                error: function (xhr) {
                    let errorMessage = "Failed to delete user!";
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                        errorMessage = Object.values(xhr.responseJSON.errors).flat().join(", ");
                    }
                    alert(errorMessage);
                }
            });
        });
    });

    // searching logic
    document.addEventListener("DOMContentLoaded", function () {
        const searchInput = document.querySelector('input[name="search"]');
        const tableRows = document.querySelectorAll('#userTableBody .roster-item');

        searchInput.addEventListener('keyup', function () {
            const term = this.value.toLowerCase();
            tableRows.forEach(function (row) {
                const rowText = row.textContent.toLowerCase();
                row.style.display = rowText.includes(term) ? '' : 'none';
            });
        });
    });
</script>