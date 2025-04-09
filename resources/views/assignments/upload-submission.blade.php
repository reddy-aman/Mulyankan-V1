<x-app-layout>
    <div class="max-w-lg mx-auto p-8 bg-white rounded-lg shadow-md mt-12">
        <!-- Header -->
        <div class="flex items-center mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a1 1 0 001 1h14a1 1 0 001-1v-1M12 12v8m0-8l-3 3m3-3l3 3M12 4v8" />
            </svg>
            <h2 class="text-2xl font-bold text-gray-800">Upload Your Submission</h2>
        </div>

        <!-- Instructions -->
        <p class="text-gray-600 mb-6">
            Please upload your completed assignment as a PDF (max 10 MB).
        </p>

        <!-- Success Message -->
        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
                {{ session('success') }}
            </div>
        @endif

        <!-- Validation Errors -->
        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Upload Form -->
        @php
            $lastOpenedCourse = session('last_opened_course');
        @endphp
        <form action="{{ route('assignments.upload', $assignmentId) }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Drag & Drop Zone -->
            <label for="submission_file"
                   class="flex flex-col items-center justify-center h-40 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:border-blue-500 hover:bg-blue-50 transition mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a1 1 0 001 1h14a1 1 0 001-1v-1M12 12v8m0-8l-3 3m3-3l3 3M12 4v8" />
                </svg>
                <span class="text-gray-600">Click to upload or drag and drop</span>
                <span class="text-xs text-gray-500 mt-1">Only PDF, max 10 MB</span>
                <input id="submission_file"
                       type="file"
                       name="submission_file"
                       accept="application/pdf"
                       required
                       class="hidden">
            </label>

            <!-- Buttons -->
            <div class="flex justify-between items-center">
                @php
                    $lastOpenedCourse = session('last_opened_course');
                @endphp
                <a href="{{ route('assignments.index',$lastOpenedCourse) }}"
                   class="flex items-center text-gray-600 hover:underline text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Assignments
                </a>
                <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Upload
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
