@props([
    'title' => 'Imagen',
])

<x-modal id="modal-image-viewer" :title="$title" size="xl">
    <div class="position-relative text-center ebt-viewer">
        <button type="button" id="btn-viewer-prev" class="btn btn-outline-light position-absolute start-0 top-50 translate-middle-y ms-3 d-none" style="z-index: 15; border-radius: 50%; width: 40px; height: 40px; padding: 0; display: flex; align-items: center; justify-content: center;" aria-label="Anterior">
            <i class="bi bi-chevron-left"></i>
        </button>
        <img id="viewer-img" src="" alt="" class="ebt-viewer__img img-fluid">
        <button type="button" id="btn-viewer-next" class="btn btn-outline-light position-absolute end-0 top-50 translate-middle-y me-3 d-none" style="z-index: 15; border-radius: 50%; width: 40px; height: 40px; padding: 0; display: flex; align-items: center; justify-content: center;" aria-label="Siguiente">
            <i class="bi bi-chevron-right"></i>
        </button>
    </div>
    <x-slot:footer>
        <span class="text-muted small me-auto" id="viewer-filename"></span>
        <a id="btn-viewer-download" href="#" download class="btn btn-outline-secondary" target="_blank">
            <i class="bi bi-download me-2"></i>Descargar
        </a>
    </x-slot:footer>
</x-modal>
