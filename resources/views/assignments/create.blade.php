<x-app-layout>
    <div 
        x-data="{ 
            step: 1, 
            selectedType: 'exam', 
            showExamTooltip: false,
            // Store your route URLs in an Alpine object:
            routes: {
                exam: '{{ route('assignments.examQuiz') }}',
                homework: '{{ route('assignments.homework') }}',
                bubble: '{{ route('assignments.bubble') }}',       // Replace '#' with your bubble route if you have one
                programming: '{{ route('assignments.programming') }}',  // Replace '#' with your programming route
                online: '{{ route('assignments.online') }}',       // Replace '#' with your online route
            }
        }" 
        class="max-w-7xl mx-auto p-6"
    >
        <!-- Step Indicator -->
        <div class="flex items-center space-x-2 mb-8 text-sm">
            <!-- Step 1 Indicator -->
            <div 
                class="flex items-center cursor-pointer" 
                :class="step === 1 ? 'font-semibold text-blue-600' : 'text-gray-500'"
                @click="step = 1"
            >
                <span>1. Assignment Type</span>
            </div>
            <!-- Separator -->
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" 
                 stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                <path d="M13 5l7 7-7 7" />
            </svg>
            <!-- Step 2 Indicator -->
            <div 
                class="flex items-center cursor-pointer"
                :class="step === 2 ? 'font-semibold text-blue-600' : 'text-gray-500'"
                @click="step = 2"
            >
                <span>2. Assignment Settings</span>
            </div>
        </div>

        <h1 class="text-2xl font-bold mb-4">Create Assignment</h1>

        <!-- STEP 1: Assignment Type -->
        <div 
            x-show="step === 1" 
            x-transition 
            class="bg-white rounded-lg shadow p-6"
        >
            <p class="text-gray-700 mb-6">Select the type of assignment you want to create.</p>

            <div class="flex">
                <!-- Left Navigation -->
                <nav class="w-1/4 border-r pr-4">
                    <ul class="space-y-2 text-sm">
                        <!-- Exam / Quiz -->
                        <li class="relative">
                            <button 
                                class="w-full text-left px-4 py-2 rounded flex items-center justify-between transition-colors"
                                :class="selectedType === 'exam' ? 'bg-blue-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                @click="selectedType = 'exam'"
                                @mouseenter="showExamTooltip = true"
                                @mouseleave="showExamTooltip = false"
                            >
                                Exam / Quiz
                            </button>
                            <!-- Tooltip for Exam / Quiz -->
                            <div 
                                x-show="showExamTooltip" 
                                x-transition 
                                class="absolute top-0 left-full ml-2 px-2 py-1 bg-black text-white text-xs rounded shadow"
                                style="white-space: nowrap;"
                            >
                                This is a timed exam or quiz for students.
                            </div>
                        </li>

                        <!-- Homework / Problem Set -->
                        <li>
                            <button 
                                class="w-full text-left px-4 py-2 rounded transition-colors"
                                :class="selectedType === 'homework' ? 'bg-blue-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                @click="selectedType = 'homework'"
                            >
                                Homework / Problem Set
                            </button>
                        </li>

                        <!-- Bubble Sheet -->
                        <li>
                            <button 
                                class="w-full text-left px-4 py-2 rounded transition-colors"
                                :class="selectedType === 'bubble' ? 'bg-blue-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                @click="selectedType = 'bubble'"
                            >
                                Bubble Sheet
                            </button>
                        </li>

                        <!-- Programming Assignment -->
                        <li>
                            <button 
                                class="w-full text-left px-4 py-2 rounded transition-colors"
                                :class="selectedType === 'programming' ? 'bg-blue-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                @click="selectedType = 'programming'"
                            >
                                Programming Assignment
                            </button>
                        </li>

                        <!-- Online Assignment -->
                        <li>
                            <button 
                                class="w-full text-left px-4 py-2 rounded transition-colors"
                                :class="selectedType === 'online' ? 'bg-blue-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                @click="selectedType = 'online'"
                            >
                                Online Assignment
                            </button>
                        </li>
                    </ul>
                </nav>

                <!-- Right Content Area -->
                <div class="w-3/4 pl-6">
                    <!-- Exam / Quiz Content -->
                    <div x-show="selectedType === 'exam'">
                        <h2 class="text-xl font-semibold mb-2">Exam / Quiz</h2>
                        <p class="text-gray-700">
                            Set assignment dates and times for students to take a timed exam or quiz. 
                            Students can submit within the designated time frame.
                        </p>
                    </div>

                    <!-- Homework / Problem Set Content -->
                    <div x-show="selectedType === 'homework'">
                        <h2 class="text-xl font-semibold mb-2">Homework / Problem Set</h2>
                        <p class="text-gray-700">
                            Students can upload their written solutions or typed responses 
                            to problem sets and homework assignments.
                        </p>
                    </div>

                    <!-- Bubble Sheet Content -->
                    <div x-show="selectedType === 'bubble'">
                        <h2 class="text-xl font-semibold mb-2">Bubble Sheet</h2>
                        <p class="text-gray-700">
                            Let students fill in a bubble sheet for multiple-choice questions, 
                            automatically graded once uploaded.
                        </p>
                    </div>

                    <!-- Programming Assignment Content -->
                    <div x-show="selectedType === 'programming'">
                        <h2 class="text-xl font-semibold mb-2">Programming Assignment</h2>
                        <p class="text-gray-700">
                            Students submit code solutions, which can be autograded 
                            or manually graded as needed.
                        </p>
                    </div>

                    <!-- Online Assignment Content -->
                    <div x-show="selectedType === 'online'">
                        <h2 class="text-xl font-semibold mb-2">Online Assignment</h2>
                        <p class="text-gray-700">
                            Provide an entirely online assignment with integrated 
                            text, images, or code blocks for students to complete.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Next Button (bottom-right) -->
            <div class="flex justify-end mt-6">
                <button 
                    type="button" 
                    @click="window.location.href = routes[selectedType]"
                    class="px-6 py-2 bg-blue-600 text-white rounded shadow hover:bg-blue-700 transition-colors"
                >
                    Next
                </button>
            </div>
        </div>
    </div>
</x-app-layout>
