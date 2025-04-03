<!-- resources/views/annotate.blade.php -->
<x-app-layout>
    <!-- PDF.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>
    <!-- Interact.js for draggable/resizable functionality -->
    <script src="https://cdn.jsdelivr.net/npm/interactjs/dist/interact.min.js"></script>

    <style>
        .pdf-scroll-container {
            height: 90vh; /* Adjust height as needed */
            overflow-y: auto;
            position: relative;
            background: white;
        }
        .pdf-page-wrapper {
            margin-bottom: 20px;
            position: relative;
        }
        .annotation-box {
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
        <div style="width:90%;" class="overflow-hidden p-4">
            <div id="pdf-scroll-container" class="pdf-scroll-container">
                <!-- Pre-created page wrappers will be inserted here -->
            </div>
        </div>

        <!-- RIGHT COLUMN: Sidebar (20% width) -->
        <div style="width:20%;" class="bg-gray-100 border-l p-4 flex flex-col">
            <h2 class="text-xl font-bold mb-4">Annotations</h2>
            <button id="add-box-btn"
                class="mb-4 px-2 py-2 bg-indigo-500 text-white rounded hover:bg-indigo-600 transition text-lg">
                + Add Box
            </button>
            <button id="save-btn"
                class="mb-4 px-2 py-2 bg-green-500 text-white rounded hover:bg-green-600 transition text-lg">
                Save Annotations
            </button>
            <div id="annotation-list" class="flex-1 overflow-y-auto border p-1 rounded bg-white text-lg text-center">
                <!-- Annotation items will appear here -->
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let pdfDoc = null;
            let annotationData = {};
            const container = document.getElementById("pdf-scroll-container");

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

            // Render a single page into its pre-created wrapper
            function renderPage(pageNum) {
                pdfDoc.getPage(pageNum).then(page => {
                    const pageWrapper = document.querySelector(`.pdf-page-wrapper[data-page="${pageNum}"]`);
                    const canvasContainer = pageWrapper.querySelector(".canvas-container");
                    const boxContainer = pageWrapper.querySelector(".box-container");

                    const canvas = document.createElement("canvas");
                    const ctx = canvas.getContext("2d");

                    // Scale PDF to fit within the left column's width (adjusting padding if needed)
                    const desiredWidth = container.clientWidth - 40;
                    const unscaledViewport = page.getViewport({ scale: 1 });
                    const scale = desiredWidth / unscaledViewport.width;
                    const viewport = page.getViewport({ scale: scale });

                    canvas.width = viewport.width;
                    canvas.height = viewport.height;

                    // Adjust the box container to match the canvas size
                    boxContainer.style.width = canvas.width + "px";
                    boxContainer.style.height = canvas.height + "px";

                    page.render({
                        canvasContext: ctx,
                        viewport: viewport
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
                const div = document.createElement("div");
                div.className = "annotation-box";
                div.style.top = boxData.top + "px";
                div.style.left = boxData.left + "px";
                div.style.width = boxData.width + "px";
                div.style.height = boxData.height + "px";
                div.dataset.x = 0;
                div.dataset.y = 0;

                // Double-click to rename the annotation box (name appears only in sidebar)
                div.addEventListener("dblclick", function () {
                    const newName = prompt("Enter a new name:", boxData.name);
                    if (newName !== null) {
                        boxData.name = newName;
                        updateAnnotationList();
                    }
                });

                container.appendChild(div);

                // Make the box draggable and resizable using Interact.js
                interact(div)
                    .draggable({
                        modifiers: [
                            interact.modifiers.restrictRect({
                                restriction: container,
                                endOnly: true
                            })
                        ],
                        listeners: {
                            move: function (event) {
                                let target = event.target;
                                let x = (parseFloat(target.dataset.x) || 0) + event.dx;
                                let y = (parseFloat(target.dataset.y) || 0) + event.dy;
                                target.style.transform = `translate(${x}px, ${y}px)`;
                                target.dataset.x = x;
                                target.dataset.y = y;
                            },
                            end: function (event) {
                                updateBoxData(pageNum, event.target);
                            }
                        }
                    })
                    .resizable({
                        edges: { left: true, right: true, bottom: true, top: true },
                        modifiers: [
                            interact.modifiers.restrictEdges({
                                outer: container
                            }),
                            interact.modifiers.restrictSize({
                                min: { width: 50, height: 30 }
                            })
                        ],
                        listeners: {
                            move: function (event) {
                                let target = event.target;
                                let newWidth = event.rect.width;
                                let newHeight = event.rect.height;
                                let x = (parseFloat(target.dataset.x) || 0) + event.deltaRect.left;
                                let y = (parseFloat(target.dataset.y) || 0) + event.deltaRect.top;
                                target.style.width = newWidth + "px";
                                target.style.height = newHeight + "px";
                                target.style.transform = `translate(${x}px, ${y}px)`;
                                target.dataset.x = x;
                                target.dataset.y = y;
                            },
                            end: function (event) {
                                updateBoxData(pageNum, event.target);
                            }
                        }
                    });
            }

            // Update annotation data after drag/resize ends
            function updateBoxData(pageNum, boxEl) {
                let x = parseFloat(boxEl.dataset.x) || 0;
                let y = parseFloat(boxEl.dataset.y) || 0;
                let baseTop = parseFloat(boxEl.style.top) || 0;
                let baseLeft = parseFloat(boxEl.style.left) || 0;
                let absoluteTop = baseTop + x;
                let absoluteLeft = baseLeft + y;
                let width = parseFloat(boxEl.style.width);
                let height = parseFloat(boxEl.style.height);

                const boxes = boxEl.parentElement.children;
                for (let i = 0; i < boxes.length; i++) {
                    if (boxes[i] === boxEl) {
                        if (annotationData[pageNum] && annotationData[pageNum][i]) {
                            annotationData[pageNum][i].top = absoluteTop;
                            annotationData[pageNum][i].left = absoluteLeft;
                            annotationData[pageNum][i].width = width;
                            annotationData[pageNum][i].height = height;
                        }
                        break;
                    }
                }
                updateAnnotationList();
            }

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
                const pageNum = parseInt(prompt("Enter page number for annotation:", "1"), 10);
                if (!pageNum || pageNum < 1 || pageNum > pdfDoc.numPages) return;
                if (!annotationData[pageNum]) {
                    annotationData[pageNum] = [];
                }
                let name = prompt("Enter name for the new annotation:", "New Annotation");
                if (!name) name = "New Annotation";
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
                .then(response => response.json())
                .then(data => {
                    if(data.success){
                        alert("Annotation saved successfully");
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
