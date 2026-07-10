@props([
    'post',
    'updateUrl',
    'deleteUrl',
])

<x-modal id="modal-edit-post-{{ $post->id }}" title="Editar Publicación" size="lg">
    <form method="POST" action="{{ $updateUrl }}" 
          enctype="multipart/form-data" 
          id="form-edit-post-{{ $post->id }}" 
          novalidate>
        @csrf
        @method('PUT')
        <input type="hidden" name="form_id" value="edit_post_{{ $post->id }}">

        <x-input
            name="title"
            label="Título de la publicación"
            :required="true"
            :value="$post->title"
            id="edit-title-{{ $post->id }}"
        />

        <x-textarea
            name="description"
            label="Descripción"
            :required="true"
            :value="$post->description"
            id="description-{{ $post->id }}"
        />

        <x-input
            name="published_at"
            type="datetime-local"
            label="Fecha de publicación"
            :value="$post->published_at ? $post->published_at->format('Y-m-d\TH:i') : ''"
            id="edit-published-at-{{ $post->id }}"
        />

        {{-- Manage existing attachments --}}
        @if($post->attachments->count() > 0)
            @php
                $existingFolders = $post->attachments->filter(fn($a) => $a->folder_name !== null)->groupBy('folder_name');
                $existingIndividuals = $post->attachments->filter(fn($a) => $a->folder_name === null);
            @endphp
            <div class="mb-3">
                <label class="form-label fw-medium">Evidencias actuales (Marcar para eliminar):</label>
                <div class="row g-2">
                    {{-- Carpetas existentes --}}
                    @foreach ($existingFolders as $folderName => $files)
                        <div class="col-12 col-sm-6" id="folder-wrapper-{{ Str::slug($folderName) }}">
                            <div class="card h-100 p-2 position-relative border ebt-existing-folder">
                                @foreach($files as $file)
                                    <input class="d-none ebt-folder-del-check-{{ Str::slug($folderName) }}" type="checkbox" name="delete_attachments[]" value="{{ $file->id }}" id="del-att-{{ $file->id }}">
                                @endforeach
                                
                                <button type="button" 
                                        class="ebt-file-preview__delete-btn ebt-folder-delete-toggle" 
                                        data-folder-slug="{{ Str::slug($folderName) }}"
                                        title="Marcar carpeta para eliminar">
                                    <i class="bi bi-x"></i>
                                </button>

                                <div class="d-flex align-items-center gap-2 py-1">
                                    <i class="bi bi-folder-fill text-warning fs-3"></i>
                                    <div class="min-w-0">
                                        <span class="fw-semibold text-dark text-truncate d-block small" title="{{ $folderName }}">
                                            {{ $folderName }}
                                        </span>
                                        <span class="text-muted small d-block" style="font-size: 0.75rem;">
                                            {{ $files->count() }} archivos
                                        </span>
                                    </div>
                                </div>

                                <div class="position-absolute inset-0 bg-danger bg-opacity-10 d-none flex-column align-items-center justify-content-center text-danger rounded ebt-delete-overlay" 
                                     id="del-folder-overlay-{{ Str::slug($folderName) }}">
                                    <span class="fw-bold ebt-delete-overlay__label" style="font-size: 0.8rem;">
                                        ELIMINAR CARPETA
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    {{-- Archivos individuales existentes --}}
                    @foreach ($existingIndividuals as $attachment)
                        <div class="col-6 col-sm-4 col-md-3" id="attachment-wrapper-{{ $attachment->id }}">
                            <div class="card h-100 p-1 position-relative border ebt-existing-attachment">
                                <input class="d-none" type="checkbox" name="delete_attachments[]" value="{{ $attachment->id }}" id="del-att-{{ $attachment->id }}">
                                
                                <button type="button" 
                                        class="ebt-file-preview__delete-btn ebt-attachment-delete-toggle" 
                                        data-attachment-id="{{ $attachment->id }}"
                                        title="Marcar para eliminar">
                                    <i class="bi bi-x"></i>
                                </button>

                                @if ($attachment->isImage())
                                     <img src="{{ $attachment->url }}" class="card-img-top object-fit-cover rounded ebt-attachment-thumb" alt="{{ $attachment->file_name }}">
                                 @else
                                     <div class="text-center py-2 {{ $attachment->icon['color'] }}">
                                         <i class="bi {{ $attachment->icon['icon'] }} fs-2"></i>
                                     </div>
                                 @endif
                                <div class="card-body p-1 text-center">
                                    <span class="small text-muted text-truncate d-block ebt-attachment-name" title="{{ $attachment->file_name }}">
                                        {{ $attachment->file_name }}
                                    </span>
                                </div>

                                <div class="position-absolute inset-0 bg-danger bg-opacity-10 d-none flex-column align-items-center justify-content-center text-danger rounded ebt-delete-overlay" 
                                     id="del-overlay-{{ $attachment->id }}">
                                    <span class="fw-bold ebt-delete-overlay__label">
                                        ELIMINAR
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Add new attachments --}}
        <div class="mb-3">
            <label for="attachments-{{ $post->id }}" class="form-label fw-medium">
                Agregar nuevos archivos adjuntos
            </label>
            <div class="d-flex gap-2 mb-2">
                <input
                    type="file"
                    id="attachments-{{ $post->id }}"
                    name="attachments[]"
                    class="form-control"
                    multiple
                    accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.zip,.rar"
                >
                <button type="button" class="btn btn-outline-secondary d-flex align-items-center gap-1 flex-shrink-0" onclick="document.getElementById('folder-picker-{{ $post->id }}').click();">
                    <i class="bi bi-folder-plus"></i>
                    <span class="d-none d-sm-inline">Subir Carpeta</span>
                </button>
                <input type="file" id="folder-picker-{{ $post->id }}" webkitdirectory directory multiple class="d-none">
            </div>
            <div class="form-text text-muted small">
                Imágenes (JPG, PNG, GIF, WebP), PDFs, Word, Excel y archivos comprimidos (ZIP, RAR). Máx. 20 MB por archivo.
            </div>
        </div>

        {{-- Preview container --}}
        <div id="file-preview-container-{{ $post->id }}" class="ebt-file-preview mb-3" aria-live="polite"></div>

    </form>

    <x-slot:footer>
        <form action="{{ $deleteUrl }}" method="POST"
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
        <x-button type="submit" form="form-edit-post-{{ $post->id }}" variant="primary" icon="bi-check-lg">
            Guardar Cambios
        </x-button>
    </x-slot:footer>
</x-modal>
