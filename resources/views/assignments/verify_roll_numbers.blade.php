<x-app-layout>
    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex items-center mb-8 space-x-4">
                <h2 class="text-3xl font-extrabold text-gray-900">
                    Verify Roll Numbers
                    <span class="block text-lg font-normal text-gray-600">
                        for Assignment <span class="text-blue-600">{{ $assignment->Name }}</span>
                    </span>
                </h2>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form id="rollForm"
                      action="{{ route('assignments.storeRollNumbers', $assignment->id) }}"
                      method="POST">
                    @csrf

                    @foreach ($submissionParts as $part)
                        <div class="submission-snippet mb-6 flex flex-col md:flex-row items-start gap-4">
                            <div class="w-full md:w-1/2">
                                <img src="{{ $part->snippetPath }}"
                                     alt="Roll Number Snippet"
                                     class="border border-gray-300 max-w-full" />
                            </div>
                            <div class="w-full md:w-1/2 relative">
                                <label for="roll_number_{{ $part->index }}" class="block mb-1">
                                    Roll Number
                                </label>

                                {{-- Preserve the chunk’s PDF path for saving later --}}
                                <input type="hidden"
                                       name="parts[{{ $part->index }}][file_path]"
                                       value="{{ $part->file_path }}" />

                                {{-- Hidden input to store the chosen roll number --}}
                                <input type="hidden"
                                       name="roll_numbers[{{ $part->index }}]"
                                       id="hidden_roll_number_{{ $part->index }}"
                                       required />

                                {{-- User-facing autocomplete box --}}
                                <input type="text"
                                       id="roll_number_{{ $part->index }}"
                                       class="roll-input border border-gray-300 rounded px-3 py-2 w-64 mb-4"
                                       placeholder="Select Roll number"
                                       autocomplete="off" />

                                <div class="dropdown-list absolute z-10 w-64 bg-white border border-gray-300 rounded max-h-48 overflow-auto hidden"></div>
                            </div>
                        </div>
                    @endforeach

                    <button type="submit"
                            class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded">
                        Save Roll Numbers
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const students = @json($students);

        function isValidRollNumber(val) {
            if (!val) return false;
            val = val.trim().toLowerCase();
            return students.some(s => s.sid.toLowerCase() === val);
        }

        @foreach ($submissionParts as $part)
        (function() {
            const idx         = {{ $part->index }};
            const input       = document.getElementById(`roll_number_${idx}`);
            const hiddenInput = document.getElementById(`hidden_roll_number_${idx}`);
            const dropdown    = input.nextElementSibling;
            let selectedIndex = -1;

            function filterStudents(query) {
                query = query.toLowerCase();
                return students
                    .filter(s =>
                        s.sid.toLowerCase().includes(query) ||
                        s.name.toLowerCase().includes(query) ||
                        s.email.toLowerCase().includes(query)
                    )
                    .slice(0, 5);
            }

            function renderDropdown(matches) {
                if (!matches.length) {
                    dropdown.innerHTML = '<div class="p-2 text-gray-500">No matching students</div>';
                } else {
                    dropdown.innerHTML = matches.map((s, i) => `
                        <div class="dropdown-item cursor-pointer p-2 hover:bg-blue-100 ${selectedIndex === i ? 'bg-blue-100' : ''}"
                             data-rollnumber="${s.sid}">
                            <div><strong>${s.sid}</strong></div>
                            <div>${s.name}</div>
                            <div class="text-sm text-gray-500">${s.email}</div>
                        </div>
                    `).join('');
                }
                dropdown.classList.remove('hidden');
            }

            function hideDropdown() {
                dropdown.classList.add('hidden');
                selectedIndex = -1;
            }

            function selectItem(i) {
                const items = dropdown.querySelectorAll('.dropdown-item');
                if (items[i]) {
                    const sid = items[i].dataset.rollnumber;
                    input.value       = sid;
                    hiddenInput.value = sid;
                }
                hideDropdown();
            }

            input.addEventListener('input', function() {
                const val = input.value.trim();
                selectedIndex = -1;
                hiddenInput.value = '';
                const matches = val ? filterStudents(val) : students.slice(0, 5);
                matches.length ? renderDropdown(matches) : hideDropdown();
            });

            input.addEventListener('focus', function() {
                selectedIndex = -1;
                renderDropdown(students.slice(0, 5));
            });

            input.addEventListener('keydown', function(e) {
                const items = dropdown.querySelectorAll('.dropdown-item');
                if (!items.length) return;

                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    selectedIndex = (selectedIndex + 1) % items.length;
                    renderDropdown(filterStudents(input.value));
                    items[selectedIndex].scrollIntoView({ block: 'nearest' });
                }
                else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    selectedIndex = (selectedIndex - 1 + items.length) % items.length;
                    renderDropdown(filterStudents(input.value));
                    items[selectedIndex].scrollIntoView({ block: 'nearest' });
                }
                else if (e.key === 'Enter') {
                    e.preventDefault();
                    if (selectedIndex >= 0) selectItem(selectedIndex);
                }
            });

            dropdown.addEventListener('click', function(e) {
                const item = e.target.closest('.dropdown-item');
                if (!item) return;
                input.value       = item.dataset.rollnumber;
                hiddenInput.value = item.dataset.rollnumber;
                hideDropdown();
            });

            document.addEventListener('click', function(e) {
                if (!input.contains(e.target) && !dropdown.contains(e.target)) {
                    hideDropdown();
                }
            });
        })();
        @endforeach

        // Validate on submit
        document.getElementById('rollForm').addEventListener('submit', function(e) {
            let allValid = true;
            document.querySelectorAll('.roll-input').forEach(input => {
                if (!isValidRollNumber(input.value.trim())) {
                    allValid = false;
                }
            });
            if (!allValid) {
                e.preventDefault();
                alert('Please fill all roll number fields with a valid roll number before saving.');
            }
        });
    });
    </script>

    <style>
        .dropdown-list {
            max-height: calc(5 * 3rem);
            overflow-y: auto;
        }
    </style>
</x-app-layout>
