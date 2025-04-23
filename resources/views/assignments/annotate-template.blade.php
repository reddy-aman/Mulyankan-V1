<!-- resources/views/annotate.blade.php -->
<x-app-layout>
    <!-- PDF.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>
    <!-- Interact.js for draggable/resizable functionality -->
    <script src="https://cdn.jsdelivr.net/npm/interactjs/dist/interact.min.js"></script>

    <style>
        .pdf-scroll-container {
            /* height: 90vh; */
            /* Adjust height as needed */
            /* overflow-y: auto; */
            position: relative;
            background: white;
        }

        .pdf-page-wrapper {
            margin-bottom: 20px;
            position: relative;
        }

        #box-overlay {
            z-index: 20;                /* above canvases */
            pointer-events: none;       /* let clicks pass to pages by default */
        }

        .annotation-box {
            pointer-events: auto; 
            z-index: 20;
            pointer-events: auto;
            position: absolute;
            border: 2px dashed #F6E05E;
            background-color: rgba(245, 158, 11, 0.3);
            transform: translate(0px, 0px);
            overflow: hidden;
        }
    </style>

    <!-- Layout: Left Sidebar, PDF Viewer, and Right Sidebar -->
    <div class="flex h-screen flex-nowrap">
        <aside class="w-64 bg-white shadow-md px-4 py-6">
            <nav>
                <!-- Left sidebar content (if any) -->
                <p class="font-bold">Left Sidebar</p>
            </nav>
        </aside>

        <!-- LEFT COLUMN: Scrollable PDF Viewer (90% width) -->
        <div style="width:90%;" class="flex-1 flex flex-col p-4">
            <div id="pdf-scroll-container" class="pdf-scroll-container flex-1 overflow-auto relative">
            <!-- page wrappers here -->
                <div class="pdf-page-wrapper" data-page="1">…</div>
                <!-- etc -->
                <!-- NEW: full‐height overlay for annotations -->
                <div id="box-overlay" class="absolute inset-0 pointer-events-none"></div>
            </div>
        </div>


        <!-- RIGHT COLUMN: Sidebar (20% width) -->
        <div style="width:20%;" class="bg-gray-100 border-l p-4 flex flex-col">
            <h2 class="text-xl font-bold mb-4"></h2>
            <button id="add-roll-btn" class="mb-2 w-full px-2 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600 transition">
                Roll No
            </button>

            <button id="add-dept-btn"
                class="mb-4 w-full px-2 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition">
                Dept
            </button>

            <button id="add-box-btn"
                class="mb-4 w-full px-2 py-2 bg-indigo-500 text-white rounded hover:bg-indigo-600 transition">
            + Add Questions
            </button>

            <button id="save-btn"
                class="mb-4 px-2 py-2 bg-green-500 text-white rounded hover:bg-green-600 transition text-lg">
                Save Outline
            </button>
            <div id="annotation-list" class="flex-1 overflow-y-auto border p-1 rounded bg-white text-lg text-center">
                <!-- Annotation items will appear here -->
            </div>
        </div>
    </div>

    <script>
        window.initialAnnotations = @json($annotations ?? []);
        document.addEventListener("DOMContentLoaded", function () {
            let pdfDoc = null;
            let annotationData = window.initialAnnotations || {};
            const container = document.getElementById("pdf-scroll-container");

            const overlay = document.createElement("div");
            overlay.id = "box-overlay";
            overlay.style.position = "absolute";
            overlay.style.inset = "0";
            overlay.style.zIndex = "20";
            overlay.style.pointerEvents = "none";
            container.style.position = "relative";
            container.appendChild(overlay);
            
            pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.worker.min.js';

            // Pre-create wrappers for all pages in order
            function preCreatePageWrappers(numPages) {
                container.innerHTML = "";
                for (let i = 1; i <= numPages; i++) {
                    const pageWrapper = document.createElement("div");
                    pageWrapper.classList.add("pdf-page-wrapper");
                    pageWrapper.dataset.page = i;
                    // Placeholders for canvas and annotation overlay
                    pageWrapper.innerHTML = `
                        <div class="canvas-container"></div>
                        <div class="box-container" style="position:absolute; top:0; left:0;"></div>
                    `;
                    container.appendChild(pageWrapper);
                }
            }

            function getCurrentPageNumber() {
                const container = document.getElementById('pdf-scroll-container');
                const containerRect = container.getBoundingClientRect();
                // pick the vertical midpoint of the container
                const midY = containerRect.top + containerRect.height / 2;

                // find the page whose canvas spans that midpoint
                const wrappers = container.querySelectorAll('.pdf-page-wrapper');
                for (const wrap of wrappers) {
                    const rect = wrap.getBoundingClientRect();
                    if (rect.top < midY && rect.bottom > midY) {
                    return parseInt(wrap.dataset.page, 10);
                    }
                }
                return 1;  // fallback
                }

            function promptAndCreateBox(typeName) {
                const pageNum = 1;
                if (!pageNum || pageNum < 1 || pageNum > pdfDoc.numPages) return;

                if (!annotationData[pageNum]) annotationData[pageNum] = [];

                // Default size/position (you can tweak)
                const newBox = {
                    top: 50,
                    left: 50,
                    width: 150,
                    height: 50,
                    name: typeName
                };

                annotationData[pageNum].push(newBox);

                const pageWrapper = document.querySelector(`.pdf-page-wrapper[data-page="${pageNum}"]`);
                const boxContainer = pageWrapper.querySelector(".box-container");
                createAnnotationBox(pageNum, newBox, annotationData[pageNum].length - 1, boxContainer);

                updateAnnotationList();
            }
            // Render a single page into its pre-created wrapper
            function renderPage(pageNum) {
                pdfDoc.getPage(pageNum).then(page => {
                    const pageWrapper = document.querySelector(`.pdf-page-wrapper[data-page="${pageNum}"]`);
                    const canvasContainer = pageWrapper.querySelector(".canvas-container");
                    const boxContainer = pageWrapper.querySelector(".box-container");

                    const canvas = document.createElement("canvas");
                    const ctx = canvas.getContext("2d");

                    const desiredWidth = container.clientWidth - 40;
                    const unscaledViewport = page.getViewport({ scale: 1 });
                    const scale = desiredWidth / unscaledViewport.width;
                    const viewport = page.getViewport({ scale });

                    canvas.width = viewport.width;
                    canvas.height = viewport.height;

                    boxContainer.style.width = canvas.width + "px";
                    boxContainer.style.height = canvas.height + "px";

                    // Store the scale for the current page
                    pageWrapper.dataset.scale = scale;

                    page.render({
                        canvasContext: ctx,
                        viewport
                    }).promise.then(() => {
                        canvasContainer.appendChild(canvas);
                        initializeBoxes(pageNum, boxContainer);
                    });
                });
            }


            // Load all pages
            function loadAllPages() {
                preCreatePageWrappers(pdfDoc.numPages);
                for (let i = 1; i <= pdfDoc.numPages; i++) {
                    renderPage(i);
                }
            }

            // Initialize annotation boxes for a given page
            function initializeBoxes(pageNum, boxContainer) {
                if (!annotationData[pageNum]) {
                    annotationData[pageNum] = [];
                }
                annotationData[pageNum].forEach((box, idx) => {
                    createAnnotationBox(pageNum, box, idx, boxContainer);
                });
                updateAnnotationList();
            }

            // Create an annotation box element with Interact.js functionality
            function createAnnotationBox(pageNum, boxData, idx, container) {
                const pageWrapper = document.querySelector(`.pdf-page-wrapper[data-page="${pageNum}"]`);
                const scale = parseFloat(pageWrapper.dataset.scale) || 1;

                const div = document.createElement("div");
                div.className = "annotation-box";

                div.style.top = (boxData.top * scale) + "px";
                div.style.left = (boxData.left * scale) + "px";
                div.style.width = (boxData.width * scale) + "px";
                div.style.height = (boxData.height * scale) + "px";

                const label = document.createElement("div");
                label.className = "annotation-label text-xs font-semibold text-white px-1 py-0.5";
                label.style.position       = "absolute";
                label.style.top            = "0";
                label.style.left           = "0";
                label.style.backgroundColor= "rgba(245, 158, 11, 0.9)";
                label.textContent          = boxData.name;
                div.appendChild(label);

                container.appendChild(div);

                div.dataset.x = 0;
                div.dataset.y = 0;
                div.dataset.idx = idx;

                div.addEventListener("dblclick", () => {
                    const newName = prompt("Enter a new name:", boxData.name);
                    if (newName !== null) {
                        boxData.name = newName;
                        updateAnnotationList();
                    }
                });

                container.appendChild(div);

                interact(div)
                    .draggable({
                        modifiers: [
                            interact.modifiers.restrictRect({
                                restriction: container,
                                endOnly: true
                            })
                        ],
                        listeners: {
                            move(event) {
                                const target = event.target;
                                let x = (parseFloat(target.dataset.x) || 0) + event.dx;
                                let y = (parseFloat(target.dataset.y) || 0) + event.dy;
                                target.style.transform = `translate(${x}px, ${y}px)`;
                                target.dataset.x = x;
                                target.dataset.y = y;
                            },
                            end(event) {
                                updateBoxData(pageNum, event.target);
                            }
                        }
                    })
                    .resizable({
                        edges: { left: true, right: true, bottom: true, top: true },
                        modifiers: [
                            interact.modifiers.restrictEdges({ outer: container }),
                            interact.modifiers.restrictSize({ min: { width: 50, height: 30 } })
                        ],
                        listeners: {
                            move(event) {
                                const target = event.target;
                                let x = (parseFloat(target.dataset.x) || 0) + event.deltaRect.left;
                                let y = (parseFloat(target.dataset.y) || 0) + event.deltaRect.top;
                                target.style.width = event.rect.width + "px";
                                target.style.height = event.rect.height + "px";
                                target.style.transform = `translate(${x}px, ${y}px)`;
                                target.dataset.x = x;
                                target.dataset.y = y;
                            },
                            end(event) {
                                updateBoxData(pageNum, event.target);
                            }
                        }
                    });
            }


            // Update annotation data after drag/resize ends
            function updateBoxData(pageNum, boxEl) {
                const pageWrapper = document.querySelector(`.pdf-page-wrapper[data-page="${pageNum}"]`);
                const scale = parseFloat(pageWrapper.dataset.scale) || 1;

                const x = parseFloat(boxEl.dataset.x) || 0;
                const y = parseFloat(boxEl.dataset.y) || 0;
                const topPx = parseFloat(boxEl.style.top) + y;
                const leftPx = parseFloat(boxEl.style.left) + x;
                const widthPx = parseFloat(boxEl.style.width);
                const heightPx = parseFloat(boxEl.style.height);

                const top = topPx / scale;
                const left = leftPx / scale;
                const width = widthPx / scale;
                const height = heightPx / scale;

                const idx = parseInt(boxEl.dataset.idx);
                if (annotationData[pageNum] && annotationData[pageNum][idx]) {
                    annotationData[pageNum][idx].top = top;
                    annotationData[pageNum][idx].left = left;
                    annotationData[pageNum][idx].width = width;
                    annotationData[pageNum][idx].height = height;
                }

                updateAnnotationList();
            }

            document.getElementById("add-roll-btn").addEventListener("click", () => {
                promptAndCreateBox("Roll No");
            });

            document.getElementById("add-dept-btn").addEventListener("click", () => {
                promptAndCreateBox("Dept");
            });

            // Update the annotation sidebar list (shows only the box's name) and add a delete button
            function updateAnnotationList() {
                const list = document.getElementById("annotation-list");
                list.innerHTML = "";
                Object.keys(annotationData).forEach(pageNum => {
                    annotationData[pageNum].forEach((box, idx) => {
                        const item = document.createElement("div");
                        item.className = "p-1 border-b flex items-center justify-between";
                        const nameSpan = document.createElement("span");
                        nameSpan.textContent = box.name;
                        nameSpan.className = "text-lg";
                        const deleteBtn = document.createElement("button");
                        deleteBtn.textContent = "Delete";
                        deleteBtn.className = "text-red-500 text-sm";
                        deleteBtn.addEventListener("click", function () {
                            deleteAnnotation(pageNum, idx);
                        });
                        item.appendChild(nameSpan);
                        item.appendChild(deleteBtn);
                        list.appendChild(item);
                    });
                });
            }

            // Delete an annotation and update the DOM and data structure
            function deleteAnnotation(pageNum, index) {
                if (annotationData[pageNum]) {
                    annotationData[pageNum].splice(index, 1);
                    const pageWrapper = document.querySelector(`.pdf-page-wrapper[data-page="${pageNum}"]`);
                    if (pageWrapper) {
                        const boxContainer = pageWrapper.querySelector(".box-container");
                        boxContainer.innerHTML = "";
                        annotationData[pageNum].forEach((box, idx) => {
                            createAnnotationBox(pageNum, box, idx, boxContainer);
                        });
                    }
                }
                updateAnnotationList();
            }

            // "Add Annotation Box" button event handler
            document.getElementById("add-box-btn").addEventListener("click", function () {
                const pageNum = getCurrentPageNumber();
                if (!pageNum || pageNum < 1 || pageNum > pdfDoc.numPages) return;
                if (!annotationData[pageNum]) {
                    annotationData[pageNum] = [];
                }
                let name = prompt("Enter name for the new annotation:", "New Outline");
                if (!name) name = "New Outline";
                let newBox = {
                    top: 50,
                    left: 50,
                    width: 150,
                    height: 50,
                    name: name
                };
                annotationData[pageNum].push(newBox);
                const pageWrapper = document.querySelector(`.pdf-page-wrapper[data-page="${pageNum}"]`);
                if (pageWrapper) {
                    const boxContainer = pageWrapper.querySelector(".box-container");
                    createAnnotationBox(pageNum, newBox, annotationData[pageNum].length - 1, boxContainer);
                    updateAnnotationList();
                }
            });

            // "Save Annotations" button event handler: send annotationData to the server via AJAX
            document.getElementById("save-btn").addEventListener("click", function () {
                fetch("{{ route('assignments.saveAnnotation') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify(annotationData)
                })
                    .then(response => {
                        console.log("Raw response:", response); // Log the raw response
                        return response.json(); // Parse and return JSON
                    })                    
                    .then(data => {
                        if (data.success) {
                            alert("Outlines saved successfully");
                            window.location.href = data.redirect_url;
                        } else {
                            alert("Error saving annotations.");
                        }
                    })
                    .catch(error => console.error("Error:", error));
            });

            // Load the PDF and then load all pages
            const pdfUrl = "{{ asset('storage/' . $filePath) }}";
            pdfjsLib.getDocument(pdfUrl).promise.then(pdf => {
                pdfDoc = pdf;
                loadAllPages();
            }).catch(err => console.error("Error loading PDF:", err));
        });
    </script>
</x-app-layout>