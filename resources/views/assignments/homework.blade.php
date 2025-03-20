<x-app-layout>
    <div class="max-w-7xl mx-auto p-6">
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
            <h1 class="text-3xl font-bold text-gray-800">Create Assignment</h1>
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
        <div class="bg-white rounded-lg shadow-lg p-8">
            <!-- Assignment Type & Title -->
            <div class="mb-6">
                <label class="block text-gray-700 font-medium mb-2">
                    Assignment Type:
                </label>
                <div class="text-lg font-semibold text-gray-900">
                    Homework / Problem Set
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
                    required
                    class="w-full p-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200"
                />
            </div>

            <!-- Template (Select PDF) - Required -->
            <div class="mb-6">
                <label class="block text-gray-700 font-medium mb-2">
                    Template <span class="text-red-500">*</span>
                </label>
                <div>
                    <button 
                        type="button" 
                        class="px-4 py-2 w-full bg-gradient-to-r from-gray-100 to-gray-200 text-gray-800 border border-gray-300 rounded hover:from-gray-200 hover:to-gray-300 transition duration-200 shadow-inner"
                    >
                        Select PDF
                    </button>
                    <p class="text-xs text-gray-500 mt-1">
                        Please select a PDF template. This field is required.
                    </p>
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
                        class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
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
                            class="w-5 h-5 text-blue-600 border-gray-300 focus:ring-blue-500"
                        />
                        <span class="text-gray-700">Instructor</span>
                    </label>
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input 
                            type="radio" 
                            name="upload_who" 
                            value="student" 
                            class="w-5 h-5 text-blue-600 border-gray-300 focus:ring-blue-500"
                        />
                        <span class="text-gray-700">Student</span>
                    </label>
                </div>
            </div>

            <!-- Due Date/Time -->
            <div class="mb-6">
                <label for="due_date" class="block text-gray-700 font-medium mb-2">
                    Due Date/Time <span class="text-red-500">*</span>
                </label>
                <input 
                    type="datetime-local" 
                    id="due_date" 
                    name="due_date"
                    required
                    class="w-full p-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
                <p class="text-xs text-gray-500 mt-1">
                    Set the final date and time by which students must submit.
                </p>
            </div>

            <!-- Submission Type -->
            <div class="mb-6">
                <label class="block text-gray-700 font-medium mb-2">
                    Submission Type
                </label>
                <div class="space-y-2">
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input 
                            type="radio" 
                            name="submission_type" 
                            value="paper" 
                            class="w-5 h-5 text-blue-600 border-gray-300 focus:ring-blue-500"
                        />
                        <span class="text-gray-700">Paper-based submission</span>
                    </label>
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input 
                            type="radio" 
                            name="submission_type" 
                            value="online" 
                            class="w-5 h-5 text-blue-600 border-gray-300 focus:ring-blue-500"
                        />
                        <span class="text-gray-700">Online submission</span>
                    </label>
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
                        class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
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
                            class="w-5 h-5 text-blue-600 border-gray-300 focus:ring-blue-500"
                        />
                        <span class="text-gray-700">Before student submission</span>
                    </label>
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input 
                            type="radio" 
                            name="rubric_creation" 
                            value="while_grading" 
                            class="w-5 h-5 text-blue-600 border-gray-300 focus:ring-blue-500"
                            checked
                        />
                        <span class="text-gray-700">While grading submissions</span>
                    </label>
                </div>
            </div>

            <!-- Template Availability -->
            <div class="mb-6">
                <label class="block text-gray-700 font-medium mb-2">
                    Template Availability
                </label>
                <div class="flex items-center space-x-2">
                    <input 
                        type="checkbox" 
                        id="allow_download" 
                        name="allow_download" 
                        class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                    />
                    <label for="allow_download" class="text-gray-700">
                        Allow students to view and download the template
                    </label>
                </div>
            </div>

            <!-- Create Assignment Button -->
            <div class="text-right">
                <button 
                    type="submit" 
                    class="px-8 py-3 bg-blue-600 text-white rounded-lg shadow-lg hover:bg-blue-700 transition-colors duration-200"
                >
                    Create Assignment
                </button>
            </div>
        </div>
    </div>
</x-app-layout>
