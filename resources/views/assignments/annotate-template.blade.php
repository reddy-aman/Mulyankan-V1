<x-app-layout>
    <!-- PDF.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>
    <!-- jQuery & jQuery UI for draggable/resizable -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

    <style>
        /* Overall layout: left column for PDF, right column for sidebar */
        .pdf-container {
            position: relative;
            margin: 0 auto;
            background: white;
        }
        #pdf-canvas {
            position: relative;
            z-index: 1;
            background: white;
            display: block; /* ensures canvas is a block element */
        }
        /* The annotation container covers the canvas exactly */
        #box-container {
            position: absolute;
            top: 0;
            left: 0;
            z-index: 10;
        }
    </style>

    <!-- Main content offset from the fixed sidebar -->
    <div style="margin-left:16rem;" class="flex h-screen">
        <!-- LEFT COLUMN: PDF Viewer -->
        <div class="flex-1 overflow-auto p-4">
            <!-- PDF Wrapper -->
            <div id="pdf-wrapper" class="pdf-container">
                <canvas id="pdf-canvas"></canvas>
                <div id="box-container"></div>
            </div>

            <!-- Page Navigation -->
            <div class="flex space-x-2 mt-4">
                <button id="prev-page" class="px-4 py-2 bg-gray-200 rounded">Previous Page</button>
                <button id="next-page" class="px-4 py-2 bg-gray-200 rounded">Next Page</button>
                <span id="page-info" class="text-gray-600"></span>
            </div>
        </div>

        <!-- RIGHT COLUMN: Sidebar (Annotations) -->
        <div class="w-64 bg-gray-100 border-l p-4 flex flex-col">
            <h2 class="text-xl font-bold mb-4">Annotations</h2>
            <button id="add-box-btn"
                class="mb-4 px-4 py-2 bg-indigo-500 text-white rounded hover:bg-indigo-600 transition">
                + Add Annotation Box
            </button>
            <div id="annotation-list" class="flex-1 overflow-y-auto border p-2 rounded bg-white text-sm">
                <!-- Annotation items will appear here -->
            </div>
        </div>
    </div>


    <script>
        let pdfDoc = null;
        let currentPage = 1;
        let totalPages = 0;
        // We'll store annotation data per page: annotationData[pageNumber] = [ {top, left, width, height, name}, ... ]
        let annotationData = {};

        // Load the PDF
        const pdfUrl = "{{ asset('storage/' . $filePath) }}";
        pdfjsLib.getDocument(pdfUrl).promise.then(pdf => {
            pdfDoc = pdf;
            totalPages = pdf.numPages;
            renderPage(currentPage);
        }).catch(err => console.error("Error loading PDF:", err));

        // Dynamically scale PDF to fit the left column
        function renderPage(pageNum) {
            pdfDoc.getPage(pageNum).then(page => {
                const wrapper = document.getElementById('pdf-wrapper');
                const canvas = document.getElementById('pdf-canvas');
                const ctx = canvas.getContext('2d');

                // Calculate scale so PDF fits the wrapper width
                const desiredWidth = wrapper.clientWidth;
                const unscaledViewport = page.getViewport({ scale: 1 });
                let scale = desiredWidth / unscaledViewport.width;

                // Create the final viewport
                const viewport = page.getViewport({ scale: scale });

                // Set canvas dimensions
                canvas.width = viewport.width;
                canvas.height = viewport.height;

                // Render
                page.render({
                    canvasContext: ctx,
                    viewport: viewport
                }).promise.then(() => {
                    document.getElementById('page-info').innerText = `Page ${pageNum} of ${totalPages}`;

                    // Adjust box-container to match canvas size
                    const boxContainer = document.getElementById('box-container');
                    boxContainer.style.width = canvas.width + 'px';
                    boxContainer.style.height = canvas.height + 'px';

                    // Initialize or re-draw annotation boxes
                    initializeBoxes(pageNum);
                });
            });
        }

        // Page Navigation
        document.getElementById('prev-page').addEventListener('click', () => {
            if (currentPage > 1) {
                saveCurrentPageBoxes();
                currentPage--;
                renderPage(currentPage);
            }
        });
        document.getElementById('next-page').addEventListener('click', () => {
            if (currentPage < totalPages) {
                saveCurrentPageBoxes();
                currentPage++;
                renderPage(currentPage);
            }
        });

        // Initialize boxes for the given page
        function initializeBoxes(pageNum) {
            const boxContainer = document.getElementById('box-container');
            boxContainer.innerHTML = "";

            if (!annotationData[pageNum]) {
                annotationData[pageNum] = [];
            }

            annotationData[pageNum].forEach((box, idx) => {
                createBoxElement(pageNum, box, idx);
            });
            updateAnnotationList();
        }

        // "Add Annotation Box" button
        document.getElementById('add-box-btn').addEventListener('click', () => {
            const pageNum = currentPage;
            if (!annotationData[pageNum]) {
                annotationData[pageNum] = [];
            }
            // Default new box
            let newBox = {
                top: 50,
                left: 50,
                width: 150,
                height: 50,
                name: "Annotation"
            };
            // Add to annotationData
            annotationData[pageNum].push(newBox);
            createBoxElement(pageNum, newBox, annotationData[pageNum].length - 1);
            updateAnnotationList();
        });

        // Create a draggable/resizable annotation box
        function createBoxElement(pageNum, boxData, index) {
            const boxContainer = document.getElementById('box-container');
            const div = document.createElement('div');
            div.className = 'border-2 border-dashed bg-yellow-100 opacity-70';
            div.style.position = 'absolute';
            div.style.top = boxData.top + 'px';
            div.style.left = boxData.left + 'px';
            div.style.width = boxData.width + 'px';
            div.style.height = boxData.height + 'px';

            // Double-click to rename
            div.addEventListener('dblclick', () => {
                const newName = prompt("Enter a new name:", boxData.name);
                if (newName !== null) {
                    boxData.name = newName;
                    updateAnnotationList();
                }
            });

            boxContainer.appendChild(div);

            // Make the box draggable & resizable using jQuery UI
            $(div).draggable({
                containment: "#box-container",
                stop: () => updateBoxData(pageNum, div)
            }).resizable({
                containment: "#box-container",
                stop: () => updateBoxData(pageNum, div)
            });
        }

        // Update the annotation data after drag/resize stops
        function updateBoxData(pageNum, boxEl) {
            const boxes = document.getElementById('box-container').children;
            for (let i = 0; i < boxes.length; i++) {
                if (boxes[i] === boxEl) {
                    annotationData[pageNum][i].top = $(boxEl).position().top;
                    annotationData[pageNum][i].left = $(boxEl).position().left;
                    annotationData[pageNum][i].width = $(boxEl).width();
                    annotationData[pageNum][i].height = $(boxEl).height();
                    break;
                }
            }
            updateAnnotationList();
        }

        // Display the annotations in the right sidebar
        function updateAnnotationList() {
            const list = document.getElementById('annotation-list');
            list.innerHTML = "";
            if (!annotationData[currentPage]) return;

            annotationData[currentPage].forEach((box, idx) => {
                const item = document.createElement('div');
                item.className = 'p-2 border-b';
                item.textContent = `Box ${idx + 1}: ${box.name} (${box.top}px, ${box.left}px, ${box.width}x${box.height})`;
                list.appendChild(item);
            });
        }

        // Save the current page's boxes if needed
        function saveCurrentPageBoxes() {
            // For draggable/resizable, you may want to update all boxes, but we do it on "stop" event
        }
    </script>
</x-app-layout>
