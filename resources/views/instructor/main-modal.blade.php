<div x-show="openModal" style="display: none;" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50" @click="openModal = false">
    <div class="bg-white p-6 rounded-lg shadow-lg max-w-lg w-full" @click.stop>
        <h2 class="text-xl font-semibold">Add Students or Staff</h2>
        <div class="mt-3 bg-blue-100 p-3 text-sm text-gray-700 rounded flex items-start">
            <i class="fa fa-info-circle mr-2 text-blue-500"></i>
            <span>Add a single user or upload a CSV file to add multiple users.</span>
        </div>
        <div class="mt-6 flex justify-around">
            <!-- Single User Option -->
            <div class="text-center cursor-pointer hover:opacity-75" @click="openModal = false; showSingleUser = true;">
                <i class="fa fa-user fa-3x text-blue-500"></i>
                <p class="text-blue-500 font-semibold mt-2">Single User</p>
            </div>
            <!-- CSV File Option -->
            <div class="text-center cursor-pointer hover:opacity-75" @click="openModal = false; showCSVUpload = true;">
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
