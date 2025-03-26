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
                        <input type="hidden" name="old_email" x-model="editUserData.oldEmail">
                        <input type="hidden" name="id" x-model="editUserData.id">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Full Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" x-model="editUserData.name"
                            class="border px-4 py-2 w-full rounded" required>
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
                        <input type="text" name="sid" x-model="editUserData.sid"
                            class="border px-4 py-2 w-full rounded">
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