<x-app-layout>
    <div class="max-w-4xl mx-auto p-6">
        <h1 class="text-3xl font-bold mb-4">Upload Multi-Page PDF Template</h1>
        @if ($errors->any())
            <div class="mb-4 text-red-600">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>- {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('assignments.storeTemplate') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <label class="block mb-2 text-gray-700 font-medium">
                Template PDF (multi-page):
            </label>
            <input type="file" name="template_pdf" accept="application/pdf" class="mb-4" required>

            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Upload Template
            </button>
        </form>
    </div>
</x-app-layout>
