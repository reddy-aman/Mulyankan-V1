<x-app-layout>
    <!-- Include PDF.js from CDN (if needed for further enhancements) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>
    <!-- Include jQuery and jQuery UI if you plan to use a draggable/resizable selection box -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

    <div x-data="{ showRubricTooltip: false }" class="max-w-4xl mx-auto p-6">
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

        <!-- Main Card with Form -->
        <div class="bg-white rounded-lg shadow-lg p-8">
            <!-- Assignment Type & Title -->
            <div class="mb-6">
                <label class="block text-gray-700 font-medium mb-2">
                    Assignment Type:
                </label>
                <div class="text-lg font-semibold text-gray-900">
                    Online Assignment
                </div>
            </div>

            <!-- Form to trigger storeTemplate -->
            <form action="{{ route('assignments.storeTemplate') }}" method="POST" enctype="multipart/form-data">
                @csrf

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
                            class="w-full px-4 py-3 bg-gradient-to-r from-gray-100 to-gray-200 text-gray-800 border border-gray-300 rounded hover:from-gray-200 hover:to-gray-300 transition duration-200 shadow-inner"
                            onclick="document.getElementById('template_pdf').click()"
                        >
                            Select PDF
                        </button>
                        <input type="file" name="template_pdf" id="template_pdf" accept="application/pdf" required class="hidden">
                        <p class="text-xs text-gray-500 mt-1">
                            Please select a PDF template. This field is required.
                        </p>
                    </div>
                </div>

                <!-- Additional fields (if needed) can be added here -->
                <!-- Example: Region selection, which could be implemented with JavaScript -->

                <!-- Create Assignment Button -->
                <div class="text-right">
                    <button 
                        type="submit" 
                        class="px-8 py-3 bg-blue-600 text-white rounded-lg shadow-lg hover:bg-blue-700 transition-colors duration-200"
                    >
                        Create Assignment
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
