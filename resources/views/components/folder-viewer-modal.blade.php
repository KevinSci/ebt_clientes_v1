@props([
    'title' => 'Contenido de la carpeta',
])

<x-modal id="modal-folder-viewer" :title="$title" size="lg">
    <div class="ebt-folder-viewer">
        <div id="folder-viewer-body" class="p-2">
            <!-- Rellenado dinámicamente desde folderViewer.js -->
            <div class="text-center py-4 text-muted">
                <div class="spinner-border spinner-border-sm me-2" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                Cargando contenido de la carpeta...
            </div>
        </div>
    </div>
    <x-slot:footer>
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
            Cerrar
        </button>
    </x-slot:footer>
</x-modal>
