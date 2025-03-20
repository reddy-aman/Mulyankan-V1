<x-app-layout>
    <div x-data="{ showAlert: true }" class="max-w-4xl mx-auto p-6">
        <!-- Step Indicator -->
        <div class="flex items-center space-x-2 mb-6 text-sm">
            <div class="text-gray-500 cursor-pointer">
                1. Assignment Type
            </div>
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
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
                    Online Assignment
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

            <!-- Due Date / Time -->
            <div class="mb-6 flex space-x-4">
                <div class="flex-1">
                    <label for="due_date" class="block text-gray-700 font-medium mb-2">
                        Due Date/Time <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="datetime-local" 
                        id="due_date" 
                        name="due_date"
                        class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required
                    />
                </div>
                <div class="flex-1">
                    <label for="max_time_threshold" class="block text-gray-700 font-medium mb-2">
                        Maximum Time Threshold (Minutes)
                    </label>
                    <input 
                        type="number" 
                        id="max_time_threshold" 
                        name="max_time_threshold"
                        class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Optional"
                    />
                </div>
            </div>

            <!-- Group Submission -->
            <div class="mb-6">
                <label class="block text-gray-700 font-medium mb-2">
                    Group Submission
                </label>
                <div class="flex items-center space-x-2">
                    <input 
                        type="checkbox" 
                        id="group_submission" 
                        name="group_submission" 
                        class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                    />
                    <label for="group_submission" class="text-gray-700">
                        Enable group submission
                    </label>
                </div>
                <p class="text-sm text-gray-500 mt-1">
                    Allow multiple students to submit as one group.
                </p>
            </div>

            <!-- Create Your Rubric -->
            <div class="mb-6">
                <label class="block text-gray-700 font-medium mb-2">
                    Create your Rubric
                </label>
                <div class="space-y-2">
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input 
                            type="radio" 
                            name="rubric_creation" 
                            value="before_submission" 
                            class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                        />
                        <span class="text-gray-700">Before student submission</span>
                    </label>
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input 
                            type="radio" 
                            name="rubric_creation" 
                            value="while_grading" 
                            class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                            checked
                        />
                        <span class="text-gray-700">While grading submissions</span>
                    </label>
                </div>
            </div>


            <!-- Create Assignment Button -->
            <div class="text-right">
                <button 
                    type="submit" 
                    class="px-6 py-2 bg-green-600 text-white rounded shadow hover:bg-green-700 transition-colors"
                >
                    Create Assignment
                </button>
            </div>
        </div>
    </div>
</x-app-layout>
