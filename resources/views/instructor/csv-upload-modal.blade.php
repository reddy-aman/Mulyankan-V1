<div x-show="showCSVUpload" style="display: none;" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50" @click="showCSVUpload = false">
    <div class="bg-white p-6 rounded-lg shadow-lg max-w-lg w-full" @click.stop>
        <h2 class="text-xl font-semibold">Upload CSV</h2>
        <div class="mt-3 bg-blue-100 p-3 text-sm text-gray-700 rounded flex items-start">
            <i class="fa fa-info-circle mr-2 text-blue-500"></i>
            <span>Select a CSV file to upload and add multiple users at once.</span>
        </div>
        <form id="csvUploadForm" class="mt-4">
            @csrf
            <input type="file" name="csv_file" accept=".csv" class="border p-2 rounded w-full file:mr-4 file:py-2 file:px-4 file:border-0 file:bg-gray-100 file:hover:bg-gray-200" required>
            <p class="mt-4 text-lg font-semibold text-red-600 bg-red-100 p-3 rounded-lg border border-red-400">
                ⚠ CSV Format: <strong>Name, Email, SID, Role</strong> (Role must be <em>Student, Instructor, or TA</em>)
            </p>
            <div class="mt-6 flex justify-end space-x-2">
                <button type="button" class="px-4 py-2 bg-gray-200 border rounded hover:bg-gray-300" @click="showCSVUpload = false">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                    Upload
                </button>
            </div>
        </form>
    </div>
</div>
