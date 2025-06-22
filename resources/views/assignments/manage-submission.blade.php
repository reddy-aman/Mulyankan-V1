<x-app-layout>
    <div class="flex min-h-screen bg-gray-100">
        <aside class="w-64 bg-white shadow-md px-4 py-6">
            {{-- Sidebar content --}}
        </aside>

        <main class="flex-1 p-8 overflow-auto">
            <h1 class="text-2xl font-bold mb-6">Active Submissions</h1>

            @if (!empty($allSubmissions))
                @foreach ($allSubmissions as $idx => $entry)
                    @php
                        $submission = $entry['submission'];
                        $submissionFileName = $submission->file_name;
                        $submissionKey = "submission-{$idx}";
                      @endphp

                    <div class="bg-white rounded-lg shadow mb-6">
                        <div class="px-6 py-4 bg-gray-100 border-b flex justify-between items-center">
                            <div class="flex items-center space-x-4">
                                <span class="text-lg font-semibold text-gray-800">
                                    {{ $submissionFileName }}
                                </span>

                                @if($entry['unassignedCount'] > 0)
                                    <span class="text-md font-medium text-blue-600">
                                        ({{ $entry['unassignedCount'] }} out of {{ $entry['totalParts'] }}
                                        roll number{{ $entry['totalParts'] > 1 ? 's' : '' }} unassigned)
                                    </span>
                                @endif
                            </div>


                            <div class="flex items-center space-x-2">
                                <!-- Show Submission Button -->
                                <button id="toggle-btn-{{ $submissionKey }}"
                                    class="text-white bg-blue-600 hover:bg-blue-700 font-semibold px-3 py-1 rounded transition duration-200"
                                    onclick="toggleSubmission('{{ $submissionKey }}')">
                                    Show Submission
                                </button>

                                <!-- Assign Roll Numbers Link -->
                                <a href="{{ route('assignments.verifyRollNumbers', ['assignment' => $assignment->id, 'submission' => $submission->id]) }}"
                                    class="text-white bg-blue-600 hover:bg-blue-700 font-semibold px-3 py-1 rounded transition duration-200">
                                    Assign Roll Numbers
                                </a>

                                <form
                                    action="{{ route('assignments.destroySubmission', ['assignment' => $assignment->id, 'submission' => $submission->id]) }}"
                                    method="POST"
                                    onsubmit="return confirm('Are you sure you want to delete this submission?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="text-lg text-white bg-red-600 px-3 py-1 rounded hover:bg-red-700 transition">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div id="content-{{ $submissionKey }}" class="hidden p-6 space-y-8">
                            @foreach ($entry['parts'] as $part)
                                <div class="bg-gray-50 border rounded shadow">
                                    <div class="px-4 py-2 border-b font-semibold text-gray-700">
                                        Submission {{ $part['part_number'] }}
                                        @if($part['roll_no'])
                                            <span class="ml-4 text-lg text-green-600">
                                                Roll No: {{ $part['roll_no'] }}
                                            </span>
                                        @endif
                                    </div>

                                    <div class="flex space-x-4 overflow-x-auto p-4">
                                        @foreach ($part['pages'] as $page)
                                                        <div class="relative w-48 h-64 border rounded flex-shrink-0">
                                                            <img src="{{ route('submissions.thumbnail', [
                                                'path' => urlencode($part['file_path']),
                                                'page' => $page,
                                                'size' => 'full',
                                            ]) }}" class="submission-thumbnail object-contain w-full h-full" data-full="{{ route('submissions.thumbnail', [
                                                'path' => urlencode($part['file_path']),
                                                'page' => $page,
                                                'size' => 'full',
                                            ]) }}" alt="Page {{ $page }}">

                                                            <button
                                                                class="absolute top-1 right-1 bg-white text-gray-800 border rounded-full p-1 shadow zoom-in"
                                                                title="View Full Image">
                                                                🔍
                                                            </button>

                                                            <span
                                                                class="absolute bottom-1 left-1 bg-blue-600 text-white text-xs font-semibold px-1 rounded">
                                                                Page {{ $page }}
                                                            </span>
                                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @else
                <p class="text-gray-500">No submissions to display.</p>
            @endif

            <!-- Zoom Lightbox Overlay -->
            <!-- Lightbox Overlay -->
            <div id="zoom-lightbox"
                class="fixed inset-0 bg-black bg-opacity-80 flex items-center justify-center z-50 overflow-auto hidden">

                <!-- Zoom Controls (Top Right) -->
                <div class="fixed top-4 right-4 flex space-x-2 z-50">
                    <button id="zoom-in" class="text-white bg-red-600 text-2xl px-3 py-1 rounded hover:bg-red-700"
                        title="Zoom In">+</button>
                    <button id="zoom-out" class="text-white bg-red-600 text-2xl px-3 py-1 rounded hover:bg-red-700"
                        title="Zoom Out">−</button>
                    <button id="close-lightbox"
                        class="text-white bg-red-600 text-4xl px-3 py-1 rounded hover:bg-red-700"
                        title="Close">&times;</button>
                </div>

                <!-- Zoomed Image Wrapper -->


                <img id="zoomed-image" src="" alt="Zoomed"
                    class="rounded shadow-lg max-w-full max-h-full object-contain transition-transform duration-300">
            </div>

        </main>
    </div>

    <script>
        function toggleSubmission(key) {
            const content = document.getElementById(`content-${key}`);
            const btn = document.getElementById(`toggle-btn-${key}`);
            const isHidden = content.classList.toggle('hidden');
            btn.textContent = isHidden ? 'Show Submission' : 'Hide Submission';
        }

        document.addEventListener('DOMContentLoaded', () => {
            const lightbox = document.getElementById('zoom-lightbox');
            const zoomedImg = document.getElementById('zoomed-image');
            const closeBtn = document.getElementById('close-lightbox');
            let currentScale = 1;

            function applyZoom(scale) {
                currentScale = Math.min(5, Math.max(1, scale)); // Clamp between 1x and 5x
                zoomedImg.style.transform = `scale(${currentScale})`;
            }

            function resetZoomState() {
                currentScale = 1;
                zoomedImg.style.transform = 'scale(1)';
            }

            function openLightboxWithImage(fullUrl) {
                resetZoomState();
                zoomedImg.onload = () => {
                    applyZoom(1);
                };
                zoomedImg.src = fullUrl;
                lightbox.classList.remove('hidden');
                document.body.style.overflow = 'hidden'; // Prevent background scroll
            }

            // Zoom in/out buttons
            document.getElementById('zoom-in').addEventListener('click', () => {
                applyZoom(currentScale + 0.1);
            });
            document.getElementById('zoom-out').addEventListener('click', () => {
                applyZoom(currentScale - 0.1);
            });

            // Thumbnail zoom click
            document.querySelectorAll('.zoom-in').forEach(btn => {
                btn.addEventListener('click', e => {
                    e.preventDefault();
                    const thumb = btn.closest('.relative').querySelector('.submission-thumbnail');
                    const fullUrl = thumb.dataset.full;
                    openLightboxWithImage(fullUrl);
                });
            });

            // Close logic
            function closeLightbox() {
                lightbox.classList.add('hidden');
                zoomedImg.src = '';
                document.body.style.overflow = ''; // Re-enable page scroll
                resetZoomState();
            }

            closeBtn.addEventListener('click', closeLightbox);

            lightbox.addEventListener('click', (e) => {
                if (e.target === lightbox) closeLightbox();
            });

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && !lightbox.classList.contains('hidden')) closeLightbox();
            });
        });

    </script>

</x-app-layout>