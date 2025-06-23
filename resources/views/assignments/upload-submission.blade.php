{{-- resources/views/assignments/upload.blade.php --}}
<x-app-layout>
    <div class="flex min-h-screen bg-gray-100">

        {{-- Sidebar --}}
        <aside class="w-64 bg-white shadow-md px-4 py-6">
            <nav>
            </nav>
        </aside>

        {{-- Main content area --}}
        <main class="flex-1 p-8 overflow-auto">

            {{-- Upload card --}}
            @if (empty($submissionParts))
                <div class="max-w-lg mx-auto p-8 bg-white rounded-lg shadow-md">
                    <div class="flex items-center mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600 mr-2" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a1 1 0 001 1h14a1 1 0 001-1v-1M12 12v8m0-8l-3 3m3-3l3 3M12 4v8" />
                        </svg>
                        <h2 class="text-2xl font-bold text-gray-800">Upload Your Submission</h2>
                    </div>

                    <p class="text-gray-600 mb-6">Please upload your completed assignment as a PDF (max 10 MB).</p>

                    @if(session('success'))
                        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('assignments.upload', $assignmentId) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <label for="submission_file"
                            class="flex flex-col items-center justify-center h-40 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:border-blue-500 hover:bg-blue-50 transition mb-6">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400 mb-2" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a1 1 0 001 1h14a1 1 0 001-1v-1M12 12v8m0-8l-3 3m3-3l3 3M12 4v8" />
                            </svg>
                            <span class="text-gray-600">Click to upload or drag and drop</span>
                            <span class="text-xs text-gray-500 mt-1">Only PDF, max 10 MB</span>
                            <input id="submission_file" type="file" name="submission_file" accept="application/pdf" required
                                class="hidden">
                            <span id="file-name" class="mt-2 text-sm text-blue-700 hidden"></span>
                        </label>

                        <div class="flex justify-between items-center">
                            <a href="{{ route('assignments.index', session('last_opened_course')) }}"
                                class="text-gray-600 hover:underline text-sm">← Back to Assignments</a>
                            <button type="submit"
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Upload</button>
                        </div>
                    </form>
                </div>
            @endif

            @if (!empty($submissionParts))
                <div class="mt-12 space-y-12">
                    @foreach ($submissionParts as $idx => $part)
                        @php
                            $submissionId = "submission-{$idx}";
                        @endphp

                        <div id="{{ $submissionId }}" class="bg-white rounded-lg shadow p-6 space-y-4">

                            {{-- Title and Rotate All Button --}}
                            <div class="flex items-center justify-between">
                                <p class="font-semibold text-lg">Proposed Submission {{ $idx + 1 }}</p>
                                <button
                                    class="rotate-submission bg-gray-800 text-white px-3 py-1 rounded hover:bg-gray-600 text-sm"
                                    data-submission-id="{{ $submissionId }}" title="Rotate All Pages">&#x21bb; Rotate
                                    All</button>
                            </div>

                            {{-- Image Thumbnails --}}
                            <div id="pages-{{ $idx }}" class="flex space-x-4 overflow-x-auto pb-2 sortable-pages"
                                data-path="{{ $part['file_path'] }}">
                                @for ($p = 1; $p <= $part['end_page'] - $part['start_page'] + 1; $p++)
                                    <div class="page-thumb flex-shrink-0 relative w-48 h-64 border rounded p-1"
                                        data-page="{{ $p }}"> 
                                        @php 
                                            $thumbUrl = route('submissions.thumbnail')
                                                        . '?path='  . urlencode($part['file_path'])
                                                        . '&page='  . $p
                                                        . '&size=full';
                                        @endphp
                                        <img
                                            src="{{ $thumbUrl }}"
                                            class="submission-thumbnail transition-transform duration-300 ease-in-out"
                                            data-rotation="0" data-path="{{ $part['file_path'] }}" data-page="{{ $p }}" />

                                        <span
                                            class="absolute bottom-1 left-1 bg-blue-600 text-white text-xs font-semibold px-1 rounded">
                                            {{ $p }}
                                        </span>

                                        {{-- Zoom button --}}
                                        <button
                                            class="absolute top-1 right-1 bg-white text-gray-800 border rounded-full p-1 shadow zoom-in"
                                            title="View Full Image">🔍</button>

                                        <button
                                            class="absolute top-1 left-1 bg-red-600 text-white border rounded-full p-1 shadow remove-page"
                                            title="Remove This Page" data-path="{{ $part['file_path'] }}"
                                            data-page="{{ $p }}">✕</button>

                                    </div>
                                @endfor
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="text-center mt-8">
                    <div class="flex items-center justify-center space-x-4">
                        <button id="rotate-all-submissions"
                            class="px-6 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition">
                            ⟳ Rotate All Submissions
                        </button>

                        <form id="finalize-form" action="{{ route('assignments.finalizeSubmission') }}" method="POST">
                            @csrf
                            <input type="hidden" name="submission_id" value="{{ $newSubmissionId }}">
                            <input type="hidden" name="assignment_id" value="{{ $assignmentId }}">
                            <input type="hidden" name="custom_folder" value="{{ $customFolderName }}">
                            <input type="hidden" name="deleted_pages" id="deleted-pages" value="{}">
                            <input type="hidden" name="page_order" id="page-order-data" value="{}">
                            <input type="hidden" name="rotations" id="rotation-data">
                            <button type="submit"
                                class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                Create Submissions
                            </button>
                        </form>
                    </div>

                </div>

                <script>
                    document.getElementById('finalize-form').addEventListener('submit', function (e) {
                        const rotations = {};
                        document.querySelectorAll('.submission-thumbnail').forEach(img => {
                            const file = img.dataset.path;
                            const page = img.dataset.page;
                            const rotation = parseInt(img.dataset.rotation || '0', 10);
                            if (rotation !== 0) {
                                rotations[file] = rotations[file] || {};
                                rotations[file][page] = rotation;
                            }
                        });
                        // console.log('Collected rotations:', rotations);
                        document.getElementById('rotation-data').value = JSON.stringify(rotations);
                    });
                </script>
            @endif


            {{-- Zoom Lightbox Overlay --}}
            <div id="zoom-lightbox"
                class="fixed inset-0 bg-black bg-opacity-80 flex items-center justify-center z-50 overflow-auto hidden">

                <!-- Controls (Top Right Corner) -->
                <div class="fixed top-4 right-4 flex space-x-2 z-50">
                    <button id="rotate-button"
                        class="text-white text-2xl font-bold bg-red-600 bg-opacity-90 px-3 py-1 rounded hover:bg-red-500"
                        title="Rotate 90°">&#x21bb;</button>

                    <button id="zoom-in-button"
                        class="text-white text-2xl font-bold bg-red-600 bg-opacity-90 px-3 py-1 rounded hover:bg-red-500"
                        title="Zoom In">+</button>

                    <button id="zoom-out-button"
                        class="text-white text-2xl font-bold bg-red-600 bg-opacity-90 px-3 py-1 rounded hover:bg-red-500"
                        title="Zoom Out">−</button>

                    <button id="close-lightbox"
                        class="text-white text-4xl font-bold bg-red-600 bg-opacity-90 px-3 py-1 rounded hover:bg-red-500"
                        aria-label="Close">&times;</button>
                </div>

                <!-- Zoomed Image -->
                <img id="zoomed-image" src="" alt="Zoomed"
                    class="rounded shadow-lg max-w-full max-h-full object-contain transition-transform duration-300">
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const lightbox = document.getElementById('zoom-lightbox');
            const zoomedImg = document.getElementById('zoomed-image');
            const closeBtn = document.getElementById('close-lightbox');
            const rotateBtn = document.getElementById('rotate-button');
            const zoomInBtn = document.getElementById('zoom-in-button');
            const zoomOutBtn = document.getElementById('zoom-out-button');
            const input = document.getElementById('submission_file');
            const fileNameSpan = document.getElementById('file-name');

            if (input) {
                input.addEventListener('change', function () {
                    if (input.files.length > 0) {
                        fileNameSpan.textContent = input.files[0].name;
                        fileNameSpan.classList.remove('hidden');
                    } else {
                        fileNameSpan.textContent = '';
                        fileNameSpan.classList.add('hidden');
                    }
                });
            }

            let currentRotation = 0;
            let currentScale = 1;

            function resetZoomState() {
                currentRotation = 0;
                currentScale = 1;
                zoomedImg.style.transform = '';
            }

            // Rotate submission thumbnails
            document.querySelectorAll('.rotate-submission').forEach(button => {
                button.addEventListener('click', () => {
                    const submissionId = button.dataset.submissionId;
                    const container = document.getElementById(submissionId);
                    const thumbnails = container.querySelectorAll('.submission-thumbnail');

                    thumbnails.forEach(img => {
                        let rotation = parseInt(img.dataset.rotation || '0');
                        rotation = (rotation + 90) % 360;
                        img.dataset.rotation = rotation;
                        img.style.transform = `rotate(${rotation}deg)`;
                    });
                });
            });

            // Zoom thumbnail
            document.querySelectorAll('.submission-thumbnail').forEach(img => {
                img.addEventListener('click', () => {
                    const url = new URL(img.src);
                    url.searchParams.set('size', 'full'); // Full image
                    zoomedImg.src = url.toString();

                    // Use same rotation as thumbnail
                    const rotation = parseInt(img.dataset.rotation || '0');
                    currentRotation = rotation;
                    currentScale = 1;
                    zoomedImg.style.transform = `rotate(${rotation}deg) scale(1)`;

                    lightbox.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                });
            });

            // Handle click on "🔍" zoom button
            document.querySelectorAll('.zoom-in').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();

                    // Find the thumbnail image in the same container
                    const container = btn.closest('.relative');
                    const img = container.querySelector('.submission-thumbnail');

                    const url = new URL(img.src);
                    url.searchParams.set('size', 'full');
                    zoomedImg.src = url.toString();

                    // Read and apply rotation from thumbnail
                    const rotation = parseInt(img.dataset.rotation || '0');
                    currentRotation = rotation;
                    currentScale = 1;
                    zoomedImg.style.transform = `rotate(${rotation}deg) scale(1)`;

                    lightbox.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                });
            });

            // Zoom In
            zoomInBtn.addEventListener('click', () => {
                currentScale += 0.1;
                zoomedImg.style.transform = `rotate(${currentRotation}deg) scale(${currentScale})`;
            });

            // Zoom Out
            zoomOutBtn.addEventListener('click', () => {
                currentScale = Math.max(0.1, currentScale - 0.1);
                zoomedImg.style.transform = `rotate(${currentRotation}deg) scale(${currentScale})`;
            });

            // Rotate Zoomed Image
            rotateBtn.addEventListener('click', () => {
                currentRotation = (currentRotation + 90) % 360;
                zoomedImg.style.transform = `rotate(${currentRotation}deg) scale(${currentScale})`;

                // Also update the corresponding thumbnail’s data-rotation
                const thumbnails = document.querySelectorAll('.submission-thumbnail');
                thumbnails.forEach(thumb => {
                    const urlThumb = new URL(thumb.src);
                    const urlZoomed = new URL(zoomedImg.src);

                    // Match page and file from thumbnail and zoomed image
                    if (urlThumb.pathname === urlZoomed.pathname && urlThumb.search === urlZoomed.search) {
                        thumb.dataset.rotation = currentRotation;
                        // Optional: also visually rotate the thumbnail
                        thumb.style.transform = `rotate(${currentRotation}deg)`;
                    }
                });
            });


            // Close lightbox
            closeBtn.addEventListener('click', () => {
                lightbox.classList.add('hidden');
                zoomedImg.src = '';
                document.body.style.overflow = '';
                resetZoomState();
            });

            // Close lightbox on click outside or ESC
            lightbox.addEventListener('click', (e) => {
                if (e.target === lightbox) {
                    lightbox.classList.add('hidden');
                    zoomedImg.src = '';
                    document.body.style.overflow = '';
                    resetZoomState();
                }
            });

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    lightbox.classList.add('hidden');
                    zoomedImg.src = '';
                    document.body.style.overflow = '';
                    resetZoomState();
                }
            });

            // Rotate *every* thumbnail on the page
            document
                .getElementById('rotate-all-submissions')
                .addEventListener('click', () => {
                    document
                        .querySelectorAll('.submission-thumbnail')
                        .forEach(img => {
                            // bump rotation by 90°, wrap at 360
                            let r = parseInt(img.dataset.rotation || '0', 10);
                            r = (r + 90) % 360;
                            img.dataset.rotation = r;
                            img.style.transform = `rotate(${r}deg)`;
                        });
                });

            // keep a simple map: { "<file_path>": [1,3,5], ... }
            const deleted = {};

            document.querySelectorAll('.remove-page').forEach(btn => {
                btn.addEventListener('click', e => {
                    e.preventDefault();
                    const path = btn.dataset.path;
                    const page = parseInt(btn.dataset.page, 10);

                    // record deletion
                    deleted[path] = deleted[path] || [];
                    if (!deleted[path].includes(page)) {
                        deleted[path].push(page);
                    }
                    document.getElementById('deleted-pages').value = JSON.stringify(deleted);

                    // remove from DOM
                    btn.closest('.relative').remove();
                });
            });
            // we'll collect: { "<file_path>": [pageNum1, pageNum2, ...], ... }
            const pageOrder = {};

            // initialize Sortable on each ".sortable-pages"
            document.querySelectorAll('.sortable-pages').forEach(container => {
                const path = container.dataset.path;

                // initial order
                pageOrder[path] = Array.from(container.children)
                    .map(el => parseInt(el.dataset.page, 10));

                Sortable.create(container, {
                    animation: 150,
                    onEnd: () => {
                        // recalculate after each drag
                        pageOrder[path] = Array.from(container.children)
                            .map(el => parseInt(el.dataset.page, 10));
                        document.getElementById('page-order-data').value = JSON.stringify(pageOrder);
                    }
                });
            });


            // ensure we always send the initial order too
            document.getElementById('page-order-data').value = JSON.stringify(pageOrder);

        });
    </script>
</x-app-layout>