<x-app-layout>
    <div class="py-4 flex">
        {{-- Sidebar --}}
        <aside class="w-64 bg-white shadow-md px-4 py-6">…</aside>

        {{-- Main --}}
        <div class="flex-1 px-6">
            <h2 class="text-3xl font-extrabold mb-6">
                Verify Roll Numbers
                <span class="block text-lg font-normal text-gray-600">
                    for Assignment <span class="text-blue-600">{{ $assignment->Name }}</span>
                </span>
            </h2>

            <div class="bg-white shadow-sm rounded-lg p-6 flex">
                <div class="w-full max-w-4xl">
                    <form action="{{ route('assignments.storeRollNumbers', $assignment->id) }}" method="POST">
                        @csrf

                        @foreach ($submissionParts as $part)
                            <div class="submission-snippet mb-6 flex items-start space-x-6">
                                <div class="relative inline-block m-0 p-0">
                                    <img src="{{ $part->snippetPath }}" alt="Roll Number Snippet"
                                        class="border border-gray-300 max-w-full rounded" />

                                        @php
                                            $thumbUrl = route('submissions.thumbnail')
                                                            . '?path='  . urlencode($part->file_path)
                                                            . '&page='  . 1
                                                            . '&size=full';
                                        @endphp
                                        <button type="button"
                                            class="absolute top-1 right-1 bg-white p-1 rounded-full shadow zoom-full"
                                            data-full-url="{{ $thumbUrl }}"
                                            title="View Full Page">
                                            <i class="fa fa-search-plus"></i>
                                        </button>

                                </div>

                                <div class="flex-1">
                                    <input type="hidden" name="parts[{{ $part->index }}][split_id]"
                                        value="{{ $part->split_id }}">

                                    <label for="roll_number_{{ $part->index }}"
                                        class="block mb-1 font-medium text-gray-700">Roll Number</label>

                                    <input type="hidden" name="roll_numbers[{{ $part->index }}]"
                                        id="hidden_roll_number_{{ $part->index }}" value="{{ $part->roll_no ?? '' }}">

                                    <input type="text" id="roll_number_{{ $part->index }}"
                                        class="roll-input border border-gray-300 rounded px-3 py-2 w-64"
                                        placeholder="Select Roll number" autocomplete="off"
                                        value="{{ $part->roll_no ?? '' }}">

                                    <div class="dropdown-list absolute z-10 bg-white border border-gray-300 rounded max-h-48 overflow-auto hidden"
                                        style="width:16rem;"></div>
                                </div>
                            </div>
                        @endforeach

                        <button type="submit"
                            class="mt-4 bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded">
                            Save Roll Numbers
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="fullpage-lightbox"
        class="fixed inset-0 bg-black bg-opacity-75 hidden z-50 flex items-center justify-center">

        <!-- Close button at top-right of screen -->
        <button id="lightbox-close"
            class="fixed top-4 right-4 bg-red-600 bg-opacity-90 text-white text-3xl z-50 px-3 py-1 rounded hover:bg-red-500"
            title="Close">&times;</button>

        <!-- Centered and stretched image -->
        <img id="lightbox-img" class="w-auto h-full max-h-screen object-contain" src="" alt="Full Page" />
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const students = @json($students);

            @foreach ($submissionParts as $part)
                (function () {
                    const idx = {{ $part->index }};
                    const input = document.getElementById(`roll_number_${idx}`);
                    const hiddenInput = document.getElementById(`hidden_roll_number_${idx}`);
                    const dropdown = input.nextElementSibling;
                    let selectedIndex = -1;

                    function filterStudents(q) {
                        q = q.trim().toLowerCase();
                        return students.filter(s =>
                            s.sid.toLowerCase().includes(q) ||
                            s.name.toLowerCase().includes(q) ||
                            s.email.toLowerCase().includes(q)
                        ).slice(0, 5);
                    }

                    function render(matches) {
                        if (!matches.length) {
                            dropdown.innerHTML = `<div class="p-2 text-gray-500">No matches</div>`;
                        } else {
                            dropdown.innerHTML = matches.map((s, i) =>
                                `<div class="dropdown-item p-2 cursor-pointer hover:bg-blue-100 ${selectedIndex === i ? 'bg-blue-100' : ''}"
                      data-sid="${s.sid}">
                  <strong>${s.sid}</strong><br>
                  <span>${s.name}</span><br>
                  <small class="text-gray-500">${s.email}</small>
                </div>`
                            ).join('');
                        }
                        dropdown.classList.remove('hidden');
                    }

                    function hide() {
                        dropdown.classList.add('hidden');
                        selectedIndex = -1;
                    }

                    function select(i) {
                        const item = dropdown.querySelectorAll('.dropdown-item')[i];
                        if (item) {
                            input.value = item.dataset.sid;
                            hiddenInput.value = item.dataset.sid;
                        }
                        hide();
                    }

                    input.addEventListener('input', () => {
                        selectedIndex = -1;
                        hiddenInput.value = '';
                        const m = filterStudents(input.value);
                        m.length ? render(m) : hide();
                    });
                    input.addEventListener('focus', () => render(students.slice(0, 5)));
                    input.addEventListener('keydown', e => {
                        const items = dropdown.querySelectorAll('.dropdown-item');
                        if (!items.length) return;
                        if (e.key === 'ArrowDown') {
                            e.preventDefault();
                            selectedIndex = (selectedIndex + 1) % items.length;
                            render(filterStudents(input.value));
                            items[selectedIndex].scrollIntoView({ block: 'nearest' });
                        } else if (e.key === 'ArrowUp') {
                            e.preventDefault();
                            selectedIndex = (selectedIndex - 1 + items.length) % items.length;
                            render(filterStudents(input.value));
                            items[selectedIndex].scrollIntoView({ block: 'nearest' });
                        } else if (e.key === 'Enter') {
                            e.preventDefault();
                            if (selectedIndex >= 0) select(selectedIndex);
                        }
                    });
                    dropdown.addEventListener('click', e => {
                        const itm = e.target.closest('.dropdown-item');
                        if (!itm) return;
                        input.value = itm.dataset.sid;
                        hiddenInput.value = itm.dataset.sid;
                        hide();
                    });
                    document.addEventListener('click', e => {
                        if (!input.contains(e.target) &&
                            !dropdown.contains(e.target) &&
                            !e.target.closest('.zoom-full')) {
                            hide();
                        }
                    });
                })();
            @endforeach

            document.querySelectorAll('.zoom-full').forEach(btn => {
                btn.addEventListener('click', () => {
                    const url = btn.dataset.fullUrl;

                    const lightbox = document.getElementById('fullpage-lightbox');
                    const img = document.getElementById('lightbox-img');

                    img.classList.add('hidden'); // hide until loaded
                    img.onload = () => {
                        img.classList.remove('hidden');
                    };

                    img.src = url;
                    lightbox.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                });
            });


            document.getElementById('lightbox-close').addEventListener('click', () => {
                document.getElementById('fullpage-lightbox').classList.add('hidden');
                document.body.style.overflow = '';
            });
        });

    </script>

    <style>
        .dropdown-list {
            max-height: 12rem;
        }
    </style>
</x-app-layout>