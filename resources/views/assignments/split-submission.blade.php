<x-app-layout>
    <div class="max-w-4xl mx-auto p-6">
        <h1 class="text-3xl font-bold mb-4">Split Student Submission</h1>
        @if ($errors->any())
            <div class="mb-4 text-red-600">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>- {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('assignments.splitSubmission') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <label class="block text-gray-700 font-medium mb-2">Select Template:</label>
            <select name="template_id" class="mb-4 p-2 border border-gray-300 rounded" required>
                @foreach(\App\Models\Template::all() as $template)
                    <option value="{{ $template->id }}">{{ $template->id }} - {{ $template->file_path }}</option>
                @endforeach
            </select>

            <label class="block text-gray-700 font-medium mb-2">Student Submission (PDF):</label>
            <input type="file" name="student_pdf" accept="application/pdf" class="mb-4" required>

            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Split Submission
            </button>
        </form>
    </div>
</x-app-layout>
