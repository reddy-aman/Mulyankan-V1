<div x-show="showSingleUser" style="display: none;"
    class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50" @click="showSingleUser = false">
    <div class="bg-white p-6 rounded-lg shadow-lg max-w-lg w-full" @click.stop>
        <h2 class="text-2xl font-semibold mb-2">Add a User</h2>
        <!-- Info Banner -->
        <div class="mt-3 bg-blue-100 p-3 text-sm text-gray-700 rounded flex items-start">
            <i class="fa fa-info-circle mr-2 text-blue-500"></i>
            <span>Add a single student or staff member.</span>
        </div>
        <!-- Single User Form -->
        <form id="addUserForm" class="mt-4">
            @csrf
            <label class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
            <input type="text" name="name" id="name" placeholder="e.g. Johnny Smith"
                class="border px-4 py-2 w-full rounded" required>

            <label class="block text-sm font-medium text-gray-700 mt-4 mb-1">Email Address <span
                    class="text-red-500">*</span></label>
            <input type="email" name="email" id="email" placeholder="e.g. email@example.com"
                class="border px-4 py-2 w-full rounded" required>

            <label class="block text-sm font-medium text-gray-700 mt-4 mb-1">Student ID # (Optional)</label>
            <input type="text" name="sid" id="sid" placeholder="e.g. 12345"
                class="border px-4 py-2 w-full rounded">

            <label class="block text-sm font-medium text-gray-700 mt-4 mb-1">Role</label>
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

            <!-- Form Buttons -->
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
