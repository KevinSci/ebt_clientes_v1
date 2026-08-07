<x-modal id="modal-edit-post-global" title="Editar Publicación" size="lg">
    <form method="POST" action="" 
          enctype="multipart/form-data" 
          id="form-edit-post-global" 
          novalidate>
        @csrf
        @method('PUT')
        <input type="hidden" name="form_id" id="global-edit-form-id" value="edit_post">

        <x-input
            name="title"
            label="Título de la publicación"
            :required="true"
            id="global-edit-title"
        />

        <x-textarea
            name="description"
            label="Descripción"
            :required="true"
            id="global-edit-description"
        />

        <x-input
            name="published_at"
            type="datetime-local"
            label="Fecha de publicación"
            id="global-edit-published-at"
        />

        {{-- Container for existing attachments (populated via JS) --}}
        <div id="global-edit-existing-attachments-container" class="mb-3 d-none">
            <label class="form-label fw-medium">Evidencias actuales (Marcar para eliminar):</label>
            <div class="row g-2" id="global-edit-existing-attachments-list"></div>
        </div>

        {{-- Add new attachments --}}
        <div class="mb-3">
            <label for="attachments-global" class="form-label fw-medium">
                Agregar nuevos archivos adjuntos
            </label>
            <div class="d-flex gap-2 mb-2">
                <input
                    type="file"
                    id="attachments-global"
                    name="attachments[]"
                    class="form-control"
                    multiple
                    accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.zip,.rar"
                >
                <button type="button" class="btn btn-outline-secondary d-flex align-items-center gap-1 flex-shrink-0" onclick="document.getElementById('folder-picker-global').click();">
                    <i class="bi bi-folder-plus"></i>
                    <span class="d-none d-sm-inline">Subir Carpeta</span>
                </button>
                <input type="file" id="folder-picker-global" webkitdirectory directory multiple class="d-none">
            </div>
            <div class="form-text text-muted small">
                Imágenes (JPG, PNG, GIF, WebP), PDFs, Word, Excel y archivos comprimidos (ZIP, RAR). Máx. 20 MB por archivo.
            </div>
        </div>

        {{-- Preview container --}}
        <div id="file-preview-container-global" class="ebt-file-preview mb-3" aria-live="polite"></div>

    </form>

    <x-slot:footer>
        <form action="" method="POST" id="form-delete-post-global"
              onsubmit="return confirm('¿Estás seguro de que deseas eliminar esta publicación de forma permanente?');"
              class="d-inline-block me-auto">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline-danger">
                <i class="bi bi-trash3 me-1"></i>Eliminar Publicación
            </button>
        </form>
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
            Cancelar
        </button>
        <x-button type="submit" form="form-edit-post-global" variant="primary" icon="bi-check-lg">
            Guardar Cambios
        </x-button>
    </x-slot:footer>
</x-modal>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const editModal = document.getElementById('modal-edit-post-global');
    if (!editModal) return;

    editModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        if (!button) return;

        const postId      = button.getAttribute('data-post-id');
        const title       = button.getAttribute('data-post-title') || '';
        const description = button.getAttribute('data-post-description') || '';
        const publishedAt = button.getAttribute('data-post-published-at') || '';
        const updateUrl   = button.getAttribute('data-update-url') || '';
        const deleteUrl   = button.getAttribute('data-delete-url') || '';
        let attachments = [];

        try {
            attachments = JSON.parse(button.getAttribute('data-attachments') || '[]');
        } catch (e) {
            attachments = [];
        }

        // Fill form fields
        document.getElementById('global-edit-form-id').value = 'edit_post_' + postId;
        document.getElementById('global-edit-title').value = title;
        document.getElementById('global-edit-description').value = description;
        document.getElementById('global-edit-published-at').value = publishedAt;
        document.getElementById('form-edit-post-global').action = updateUrl;
        document.getElementById('form-delete-post-global').action = deleteUrl;

        // Reset file inputs & previews
        document.getElementById('attachments-global').value = '';
        const folderPicker = document.getElementById('folder-picker-global');
        if (folderPicker) folderPicker.value = '';
        const previewContainer = document.getElementById('file-preview-container-global');
        if (previewContainer) previewContainer.innerHTML = '';

        // Render existing attachments
        const attachmentsContainer = document.getElementById('global-edit-existing-attachments-container');
        const attachmentsList = document.getElementById('global-edit-existing-attachments-list');
        attachmentsList.innerHTML = '';

        if (attachments && attachments.length > 0) {
            attachmentsContainer.classList.remove('d-none');
            
            // Group folder attachments
            const folderGroups = {};
            const individualFiles = [];

            attachments.forEach(att => {
                if (att.folder_name) {
                    if (!folderGroups[att.folder_name]) folderGroups[att.folder_name] = [];
                    folderGroups[att.folder_name].push(att);
                } else {
                    individualFiles.push(att);
                }
            });

            // Render folders
            Object.keys(folderGroups).forEach(folderName => {
                const files = folderGroups[folderName];
                const folderSlug = folderName.toLowerCase().replace(/[^a-z0-9]+/g, '-');
                const col = document.createElement('div');
                col.className = 'col-12 col-sm-6';

                let checkboxesHtml = files.map(f => 
                    `<input class="d-none ebt-folder-del-check-${folderSlug}" type="checkbox" name="delete_attachments[]" value="${f.id}" id="del-att-${f.id}">`
                ).join('');

                col.innerHTML = `
                    <div class="card h-100 p-2 position-relative border ebt-existing-folder">
                        ${checkboxesHtml}
                        <button type="button" class="ebt-file-preview__delete-btn ebt-folder-delete-toggle" data-folder-slug="${folderSlug}" title="Marcar carpeta para eliminar">
                            <i class="bi bi-x"></i>
                        </button>
                        <div class="d-flex align-items-center gap-2 py-1">
                            <i class="bi bi-folder-fill text-warning fs-3"></i>
                            <div class="min-w-0">
                                <span class="fw-semibold text-dark text-truncate d-block small" title="${folderName}">${folderName}</span>
                                <span class="text-muted small d-block" style="font-size: 0.75rem;">${files.length} archivos</span>
                            </div>
                        </div>
                        <div class="position-absolute inset-0 bg-danger bg-opacity-10 d-none flex-column align-items-center justify-content-center text-danger rounded ebt-delete-overlay" id="del-folder-overlay-${folderSlug}">
                            <span class="fw-bold ebt-delete-overlay__label" style="font-size: 0.8rem;">ELIMINAR CARPETA</span>
                        </div>
                    </div>
                `;
                attachmentsList.appendChild(col);
            });

            // Render individual files
            individualFiles.forEach(att => {
                const col = document.createElement('div');
                col.className = 'col-6 col-sm-4 col-md-3';
                const isImg = att.type === 'image';
                const iconClass = att.icon ? att.icon.icon : 'bi-file-earmark-arrow-down-fill';
                const colorClass = att.icon ? att.icon.color : 'text-secondary';
                const thumbContent = isImg 
                    ? `<img src="${att.url}" class="card-img-top object-fit-cover rounded ebt-attachment-thumb" alt="${att.file_name}">`
                    : `<div class="text-center py-2 ${colorClass}"><i class="bi ${iconClass} fs-2"></i></div>`;

                col.innerHTML = `
                    <div class="card h-100 p-1 position-relative border ebt-existing-attachment">
                        <input class="d-none" type="checkbox" name="delete_attachments[]" value="${att.id}" id="del-att-${att.id}">
                        <button type="button" class="ebt-file-preview__delete-btn ebt-attachment-delete-toggle" data-attachment-id="${att.id}" title="Marcar para eliminar">
                            <i class="bi bi-x"></i>
                        </button>
                        ${thumbContent}
                        <div class="card-body p-1 text-center">
                            <span class="small text-muted text-truncate d-block ebt-attachment-name" title="${att.file_name}">${att.file_name}</span>
                        </div>
                        <div class="position-absolute inset-0 bg-danger bg-opacity-10 d-none flex-column align-items-center justify-content-center text-danger rounded ebt-delete-overlay" id="del-overlay-${att.id}">
                            <span class="fw-bold ebt-delete-overlay__label">ELIMINAR</span>
                        </div>
                    </div>
                `;
                attachmentsList.appendChild(col);
            });
        } else {
            attachmentsContainer.classList.add('d-none');
        }
    });

    // Delegation for delete toggle buttons in edit modal
    document.addEventListener('click', function(e) {
        const attBtn = e.target.closest('.ebt-attachment-delete-toggle');
        if (attBtn) {
            const attId = attBtn.getAttribute('data-attachment-id');
            const checkbox = document.getElementById('del-att-' + attId);
            const overlay = document.getElementById('del-overlay-' + attId);
            if (checkbox && overlay) {
                checkbox.checked = !checkbox.checked;
                overlay.classList.toggle('d-none', !checkbox.checked);
                overlay.classList.toggle('d-flex', checkbox.checked);
            }
            return;
        }

        const folderBtn = e.target.closest('.ebt-folder-delete-toggle');
        if (folderBtn) {
            const folderSlug = folderBtn.getAttribute('data-folder-slug');
            const checkboxes = document.querySelectorAll('.ebt-folder-del-check-' + folderSlug);
            const overlay = document.getElementById('del-folder-overlay-' + folderSlug);
            if (checkboxes.length > 0) {
                const newState = !checkboxes[0].checked;
                checkboxes.forEach(cb => cb.checked = newState);
                if (overlay) {
                    overlay.classList.toggle('d-none', !newState);
                    overlay.classList.toggle('d-flex', newState);
                }
            }
        }
    });
});
</script>
@endpush
