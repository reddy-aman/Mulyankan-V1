<x-app-layout>
    <div x-data="{ showAlert: true }" class="max-w-4xl mx-auto p-6">
        <!-- Step Indicator -->
        <div class="flex items-center space-x-2 mb-6 text-sm">
            <div class="text-gray-500 cursor-pointer">
                1. Assignment Type
            </div>
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                <path d="M13 5l7 7-7 7" />
            </svg>
            <div class="font-semibold text-blue-600">
                2. Assignment Settings
            </div>
        </div>

        <!-- Header & Subtitle -->
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-bold">Create Assignment</h1>
            <!-- "Go Back" button -->
            <button 
                type="button" 
                class="text-blue-600 hover:underline text-sm"
                onclick="window.history.back()"
            >
                Go Back
            </button>
        </div>

        <!-- Main Card -->
        <div class="bg-white rounded-lg shadow p-6">
            <!-- Assignment Type & Title -->
            <div class="mb-6">
                <label class="block text-gray-700 font-medium mb-2">
                    Assignment Type:
                </label>
                <div class="text-gray-900 font-semibold">
                    Bubble Sheet
                </div>
            </div>

            <!-- Name Your Assignment -->
            <div class="mb-6">
                <label for="assignment_name" class="block text-gray-700 font-medium mb-2">
                    Assignment Name <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    id="assignment_name" 
                    name="assignment_name" 
                    placeholder="Name your assignment" 
                    class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required
                />
            </div>

            <!-- Template (Select PDF) -->
            <div class="mb-6">
                <label class="block text-gray-700 font-medium mb-2">
                    Template <span class="text-gray-500 text-sm">(Optional)</span>
                </label>
                <div>
                    <button 
                        type="button" 
                        class="px-4 py-2 bg-gray-100 text-gray-700 border border-gray-300 rounded hover:bg-gray-200 transition"
                    >
                        Select PDF
                    </button>
                </div>
            </div>

            <!-- Submission Anonymization -->
            <div class="mb-6">
                <label class="block text-gray-700 font-medium mb-2">
                    Submission Anonymization
                </label>
                <div class="flex items-center space-x-2">
                    <input 
                        type="checkbox" 
                        id="anonymous_grading" 
                        name="anonymous_grading" 
                        class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                    />
                    <label for="anonymous_grading" class="text-gray-700">
                        Enable anonymous grading
                    </label>
                </div>
                <p class="text-sm text-gray-500 mt-1">
                    Hide identifiable student information from being linked with submissions.
                </p>
            </div>

            <!-- Who Will Upload Submissions? -->
            <div class="mb-6">
                <label class="block text-gray-700 font-medium mb-2">
                    Who will upload submissions?
                </label>
                <div class="space-y-2">
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input 
                            type="radio" 
                            name="upload_who" 
                            value="instructor" 
                            class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                        />
                        <span class="text-gray-700">Instructor</span>
                    </label>
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input 
                            type="radio" 
                            name="upload_who" 
                            value="student" 
                            class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                        />
                        <span class="text-gray-700">Student</span>
                    </label>
                </div>
            </div>

            <!-- Create Assignment Button -->
            <div class="text-right">
                <button 
                    type="submit" 
                    class="px-6 py-2 bg-blue-600 text-white rounded shadow hover:bg-blue-700 transition-colors"
                >
                    Create Assignment
                </button>
            </div>
        </div>
    </div>
</x-app-layout>
