{{-- resources/views/assignments/manage-submission.blade.php --}}
<x-app-layout>
  <div class="flex min-h-screen bg-gray-100">
    <aside class="w-64 bg-white shadow-md px-4 py-6">
      {{-- … --}}
    </aside>

    <main class="flex-1 p-8 overflow-auto">
      <h2 class="text-2xl font-bold mb-6">
        Active Submissions for “{{ $assignment->Name }}”
      </h2>

      @if(!empty($submissionParts))
        <div class="space-y-8">
          @foreach($submissionParts as $part)
            <div class="bg-white rounded-lg shadow">
              <div class="px-6 py-3 bg-gray-50 border-b">
                <strong>Part {{ $part['part_number'] }}</strong>
                &mdash; {{ count($part['pages']) }} page{{ count($part['pages'])>1?'s':'' }}
              </div>
              <div class="flex space-x-4 overflow-x-auto p-6">
                @foreach($part['pages'] as $page)
                  <div class="flex-shrink-0 relative w-48 h-64 border rounded">
                    <img
                      src="{{ route('submissions.thumbnail', [
                          'path' => urlencode($part['file_path']),
                          'page' => $page,
                          'size' => 'full'
                      ]) }}"
                      alt="Part {{ $part['part_number'] }} ‒ Page {{ $page }}"
                      class="submission-thumbnail object-contain w-full h-full"
                      data-full="{{ route('submissions.thumbnail', [
                          'path' => urlencode($part['file_path']),
                          'page' => $page,
                          'size' => 'full'
                      ]) }}"
                    >

                    <button
                      class="absolute top-1 right-1 bg-white text-gray-800 border rounded-full p-1 shadow zoom-in"
                      title="View Full Image">🔍
                    </button>

                    <div class="absolute bottom-1 left-1 bg-blue-600 text-white text-xs font-semibold px-1 rounded">
                      Page {{ $page }}
                    </div>
                  </div>
                @endforeach
              </div>
            </div>
          @endforeach
        </div>
      @else
        <p class="text-gray-500">No split submissions to display.</p>
      @endif

      {{-- Zoom Lightbox Overlay --}}
      <div id="zoom-lightbox"
           class="fixed inset-0 bg-black bg-opacity-80 flex items-center justify-center z-50 hidden">
        <!-- Controls (Top Right Corner) -->
        <div class="absolute top-4 right-4 flex items-center space-x-2 z-50">
          <!-- Close -->
          <button id="close-lightbox"
                  class="text-white text-4xl hover:text-red-400"
                  title="Close">&times;</button>
          <!-- Zoom In -->
          <button id="zoom-in-button"
                  class="text-white text-2xl font-bold bg-gray-800 bg-opacity-70 px-3 py-1 rounded hover:bg-gray-600"
                  title="Zoom In">+</button>
          <!-- Zoom Out -->
          <button id="zoom-out-button"
                  class="text-white text-2xl font-bold bg-gray-800 bg-opacity-70 px-3 py-1 rounded hover:bg-gray-600"
                  title="Zoom Out">−</button>
        </div>

        <!-- Zoomed Image -->
        <img id="zoomed-image"
             src=""
             alt="Zoomed"
             class="max-w-full max-h-full object-contain rounded shadow-lg"
             style="transform: scale(1);">
      </div>
    </main>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const lightbox    = document.getElementById('zoom-lightbox');
      const zoomedImg   = document.getElementById('zoomed-image');
      const closeBtn    = document.getElementById('close-lightbox');
      const zoomInBtn   = document.getElementById('zoom-in-button');
      const zoomOutBtn  = document.getElementById('zoom-out-button');
      let   currentScale = 1;

      // open lightbox when clicking any 🔍
      document.querySelectorAll('.zoom-in').forEach(btn => {
        btn.addEventListener('click', e => {
          e.preventDefault();
          const container = btn.closest('.relative');
          const thumb     = container.querySelector('.submission-thumbnail');
          const fullUrl   = thumb.dataset.full;
          zoomedImg.src   = fullUrl;
          currentScale    = 1;
          zoomedImg.style.transform = `scale(${currentScale})`;
          lightbox.classList.remove('hidden');
          document.body.style.overflow = 'hidden';
        });
      });

      // zoom in
      zoomInBtn.addEventListener('click', () => {
        currentScale = Math.min(3, currentScale + 0.2);
        zoomedImg.style.transform = `scale(${currentScale})`;
      });

      // zoom out
      zoomOutBtn.addEventListener('click', () => {
        currentScale = Math.max(0.2, currentScale - 0.2);
        zoomedImg.style.transform = `scale(${currentScale})`;
      });

      // close lightbox
      function closeLightbox() {
        lightbox.classList.add('hidden');
        zoomedImg.src = '';
        document.body.style.overflow = '';
      }
      closeBtn.addEventListener('click', closeLightbox);
      lightbox.addEventListener('click', e => {
        if (e.target === lightbox) closeLightbox();
      });
      document.addEventListener('keydown', e => {
        if (e.key === 'Escape' && !lightbox.classList.contains('hidden')) {
          closeLightbox();
        }
      });
    });
  </script>
</x-app-layout>
