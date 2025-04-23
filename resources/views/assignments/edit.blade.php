<x-app-layout>
  <div class="max-w-2xl mx-auto p-6 bg-white rounded shadow">
    <h2 class="text-xl font-bold mb-4">Edit Assignment</h2>

    <form action="{{ route('assignments.update', $assignment->id) }}" method="POST">
      @csrf
      @method('PUT')

      <label class="block mb-2">
        <span class="font-medium">Name</span>
        <input type="text" name="Name" value="{{ old('Name', $assignment->Name) }}"
               class="mt-1 block w-full border-gray-300 rounded-md" required>
      </label>

      <label class="block mb-2">
        <span class="font-medium">Release Date</span>
        <input type="date" name="release_date" value="{{ old('release_date', optional($assignment->release_date)->format('Y-m-d')) }}"
               class="mt-1 block w-full border-gray-300 rounded-md">
      </label>

      <div class="flex justify-end space-x-2">
        <a href="{{ route('assignments.index', session('last_opened_course')) }}"
           class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-100">
          Cancel
        </a>
        <button type="submit"
                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
          Save Changes
        </button>
      </div>
    </form>
  </div>
</x-app-layout>
