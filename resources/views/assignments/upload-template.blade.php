<x-app-layout>
    <x-slot name="header">
        <h3>Verify Roll Numbers for Assignment {{ $assignment->name }}</h3>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('submissions.store_roll_numbers', $assignment->id) }}" method="POST">
                    @csrf
                    @foreach ($submissionParts as $part)
                        <div class="submission-snippet mb-6">
                            <h4 class="text-lg font-semibold mb-2">Submission ID: {{ $part['submission']->id }}</h4>
                            <img src="{{ asset($part['snippetPath']) }}" alt="Roll Number Snippet" class="border border-gray-300 max-w-md mb-2">
                            <label for="roll_number_{{ $part['submission']->id }}" class="block mb-1">Select Roll Number:</label>
                            <select name="roll_numbers[{{ $part['submission']->id }}]" id="roll_number_{{ $part['submission']->id }}" class="border border-gray-300 rounded px-3 py-2 w-full mb-4" required>
                                @foreach ($students as $student)
                                    <option value="{{ $student->roll_number }}">
                                        {{ $student->roll_number }} - {{ $student->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endforeach
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded">
                        Save Roll Numbers
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
