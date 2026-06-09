@extends('layouts.admin')

@section('title', $project->name)

@section('admin-content')

{{-- Breadcrumb --}}
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('admin.clients.index') }}">Clientes</a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{ route('admin.clients.show', $client) }}">{{ $client->name }}</a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">{{ $project->name }}</li>
    </ol>
</nav>

{{-- ── Project header ───────────────────────────────────────────────────── --}}
<div class="card mb-4" id="project-header">
    <div class="card-body">
        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3">
            <div>
                <h1 class="h4 mb-1 fw-bold">{{ $project->name }}</h1>
                <p class="text-muted small mb-2">
                    Cliente: <strong>{{ $client->name }}</strong>
                    @if ($client->company_name) — {{ $client->company_name }} @endif
                </p>
                <div class="d-flex align-items-center gap-2 mt-1">
                    <x-badge :status="$project->status" />

                    <button type="button" class="btn btn-outline-secondary btn-sm"
                            data-bs-toggle="modal"
                            data-bs-target="#modal-edit-project"
                            id="btn-open-edit-project">
                        <i class="bi bi-pencil me-1"></i>Editar Proyecto
                    </button>

                    <form action="{{ route('admin.clients.projects.destroy', [$client, $project]) }}" method="POST"
                          onsubmit="return confirm('¿Estás seguro de que deseas eliminar este proyecto y todas sus publicaciones de forma permanente?');"
                          class="d-inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm">
                            <i class="bi bi-trash3 me-1"></i>Eliminar Proyecto
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="mt-3">
            <x-progress-bar :percentage="$project->progress_percentage" :status="$project->status" />
        </div>
    </div>
</div>

<div class="row g-4">

    {{-- ── Left: Post form ──────────────────────────────────────────────── --}}
    <div class="col-12 col-xl-5">
        <div class="card ebt-sticky-xl-top">
            <div class="card-header">
                <h5 class="card-title mb-0">Nueva Publicación</h5>
            </div>
            <div class="card-body">

                <form method="POST"
                      action="{{ route('admin.clients.projects.posts.store', [$client, $project]) }}"
                      enctype="multipart/form-data"
                      id="form-new-post"
                      novalidate>
                    @csrf

                    <x-input
                        name="title"
                        label="Título de la publicación"
                        :required="true"
                        placeholder="Ej. Avance semana 3 — Inspección submarina"
                    />

                    <div class="mb-3">
                        <label for="description" class="form-label fw-medium">
                            Descripción <span class="text-danger" aria-hidden="true">*</span>
                        </label>
                        <textarea
                            id="description"
                            name="description"
                            rows="5"
                            required
                            class="form-control @error('description') is-invalid @enderror"
                            placeholder="Describe el avance, observaciones o hallazgos del proyecto…"
                        >{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <x-input
                        name="published_at"
                        type="datetime-local"
                        label="Fecha de publicación"
                        :value="now()->format('Y-m-d\TH:i')"
                    />

                    {{-- ── File upload with Vanilla JS preview ──────────────── --}}
                    <div class="mb-3">
                        <label for="attachments" class="form-label fw-medium">
                            Archivos adjuntos
                        </label>
                        <input
                            type="file"
                            id="attachments"
                            name="attachments[]"
                            class="form-control @error('attachments') is-invalid @enderror @error('attachments.*') is-invalid @enderror"
                            multiple
                            accept="image/*,.pdf"
                        >
                        <div class="form-text">
                            Imágenes (JPG, PNG, GIF, WebP) y PDFs. Máx. 20 MB por archivo.
                        </div>
                        @error('attachments')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        @error('attachments.*')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Preview container (populated by JS) --}}
                    <div id="file-preview-container" class="ebt-file-preview mb-3" aria-live="polite"></div>

                    <x-button type="submit" variant="primary" class="w-100" icon="bi-send">
                        Publicar
                    </x-button>

                </form>
            </div>
        </div>
    </div>

    {{-- ── Right: Posts list ────────────────────────────────────────────── --}}
    <div class="col-12 col-xl-7">
        <h2 class="h5 mb-3">
            Publicaciones
            <span class="badge bg-secondary ms-1">{{ $project->posts->count() }}</span>
        </h2>

        @if ($project->posts->isEmpty())
            <x-alert type="info">Aún no hay publicaciones en este proyecto.</x-alert>
@else
            <x-scrollable maxHeight="650px">
                <div class="d-flex flex-column gap-3">
                    @foreach ($project->posts as $post)
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                                    <h3 class="h6 mb-0 fw-bold">{{ $post->title }}</h3>
                                    <div class="d-flex align-items-center gap-2">
                                        @if ($post->published_at)
                                            <span class="small text-muted text-nowrap">
                                                <i class="bi bi-calendar3 me-1"></i>
                                                {{ $post->published_at->format('d/m/Y H:i') }}
                                            </span>
                                        @endif
                                        <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2" style="font-size: 0.75rem;"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modal-edit-post-{{ $post->id }}">
                                            <i class="bi bi-pencil me-1"></i>Editar
                                        </button>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <x-read-more :text="$post->description" class="small" />
                                </div>

                                @if ($post->attachments->count() > 0)
                                    <x-attachment-grid :attachments="$post->attachments" :postId="$post->id" />
                                @endif
                            </div>
                        </div>

                        {{-- Modal: Edit Post --}}
                        <x-modal id="modal-edit-post-{{ $post->id }}" title="Editar Publicación" size="lg">
                            <form method="POST" action="{{ route('admin.clients.projects.posts.update', [$client, $project, $post]) }}" 
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
                                />

                                <div class="mb-3">
                                    <label for="description-{{ $post->id }}" class="form-label fw-medium">
                                        Descripción <span class="text-danger" aria-hidden="true">*</span>
                                    </label>
                                    <textarea
                                        id="description-{{ $post->id }}"
                                        name="description"
                                        rows="5"
                                        required
                                        class="form-control @error('description') is-invalid @enderror"
                                    >{{ old('description', $post->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <x-input
                                    name="published_at"
                                    type="datetime-local"
                                    label="Fecha de publicación"
                                    :value="$post->published_at ? $post->published_at->format('Y-m-d\TH:i') : ''"
                                />

                                {{-- Manage existing attachments --}}
                                @if($post->attachments->count() > 0)
                                    <div class="mb-3">
                                        <label class="form-label fw-medium">Evidencias actuales (Marcar para eliminar):</label>
                                        <div class="row g-2">
                                            @foreach ($post->attachments as $attachment)
                                                <div class="col-6 col-sm-4 col-md-3" id="attachment-wrapper-{{ $attachment->id }}">
                                                    <div class="card h-100 p-1 position-relative border ebt-existing-attachment">
                                                        <!-- Hidden checkbox to submit deletion state -->
                                                        <input class="d-none" type="checkbox" name="delete_attachments[]" value="{{ $attachment->id }}" id="del-att-{{ $attachment->id }}">
                                                        
                                                        <!-- Red X button similar to new staged upload files -->
                                                        <button type="button" 
                                                                class="ebt-file-preview__delete-btn" 
                                                                onclick="toggleAttachmentDeletion({{ $attachment->id }})"
                                                                title="Marcar para eliminar">
                                                            <i class="bi bi-x"></i>
                                                        </button>

                                                        @if ($attachment->isImage())
                                                            <img src="{{ $attachment->url }}" class="card-img-top object-fit-cover rounded" style="height: 60px;" alt="{{ $attachment->file_name }}">
                                                        @else
                                                            <div class="text-center py-2 text-danger">
                                                                <i class="bi bi-file-earmark-pdf-fill fs-2"></i>
                                                            </div>
                                                        @endif
                                                        <div class="card-body p-1 text-center">
                                                            <span class="small text-muted text-truncate d-block" style="font-size: 0.65rem;" title="{{ $attachment->file_name }}">
                                                                {{ $attachment->file_name }}
                                                            </span>
                                                        </div>

                                                        <!-- Overlay indicator to show marked for deletion -->
                                                        <div class="position-absolute inset-0 bg-danger bg-opacity-10 d-none flex-column align-items-center justify-content-center text-danger rounded" 
                                                             id="del-overlay-{{ $attachment->id }}" 
                                                             style="pointer-events: none; z-index: 8;">
                                                            <span class="fw-bold" style="font-size: 0.65rem; background: white; padding: 2px 6px; border: 1px solid currentColor; border-radius: 4px; transform: rotate(-10deg);">
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
                                    <input
                                        type="file"
                                        id="attachments-{{ $post->id }}"
                                        name="attachments[]"
                                        class="form-control"
                                        multiple
                                        accept="image/*,.pdf"
                                    >
                                    <div class="form-text">
                                        Imágenes (JPG, PNG, GIF, WebP) y PDFs. Máx. 20 MB por archivo.
                                    </div>
                                </div>

                                {{-- Preview container --}}
                                <div id="file-preview-container-{{ $post->id }}" class="ebt-file-preview mb-3" aria-live="polite"></div>

                            </form>

                            <x-slot:footer>
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                    Cancelar
                                </button>
                                <x-button type="submit" form="form-edit-post-{{ $post->id }}" variant="primary" icon="bi-check-lg">
                                    Guardar Cambios
                                </x-button>
                            </x-slot:footer>
                        </x-modal>
                    @endforeach
                </div>
            </x-scrollable>
        @endif
    </div>

</div>

<x-image-viewer-modal title="Vista de imagen" />

{{-- Modal: Edit Project --}}
<x-modal id="modal-edit-project" title="Editar Proyecto" size="md">
    <form method="POST" action="{{ route('admin.clients.projects.update', [$client, $project]) }}" id="form-edit-project" novalidate>
        @csrf
        @method('PUT')
        <input type="hidden" name="form_id" value="edit_project">

        <div class="row g-3">
            <div class="col-12">
                <x-input name="name" label="Nombre del proyecto" :required="true" placeholder="Ej. Implementación Fase 1" :value="$project->name" />
            </div>
            <div class="col-12 col-md-6">
                <div class="mb-3">
                    <label for="status" class="form-label fw-medium">
                        Estado <span class="text-danger ms-1" aria-hidden="true">*</span>
                    </label>
                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                        <option value="active" {{ old('status', $project->status) === 'active' ? 'selected' : '' }}>Activo</option>
                        <option value="paused" {{ old('status', $project->status) === 'paused' ? 'selected' : '' }}>Pausado</option>
                        <option value="completed" {{ old('status', $project->status) === 'completed' ? 'selected' : '' }}>Completado</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-12 col-md-6">
                <x-input name="progress_percentage" type="number" label="Porcentaje de avance" :required="true"
                         placeholder="0 - 100" min="0" max="100" :value="$project->progress_percentage" />
            </div>
        </div>
    </form>

    <x-slot:footer>
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
            Cancelar
        </button>
        <x-button type="submit" form="form-edit-project" variant="primary" icon="bi-check-lg">
            Guardar Cambios
        </x-button>
    </x-slot:footer>
</x-modal>

@endsection

@push('scripts')
<script>
    // Toggle state and styling of existing attachments marked for deletion
    window.toggleAttachmentDeletion = function(id) {
        const checkbox = document.getElementById('del-att-' + id);
        const card = checkbox.closest('.ebt-existing-attachment');
        const overlay = document.getElementById('del-overlay-' + id);
        const btn = card.querySelector('.ebt-file-preview__delete-btn');
        
        checkbox.checked = !checkbox.checked;
        
        if (checkbox.checked) {
            card.classList.add('border-danger');
            card.style.opacity = '0.5';
            overlay.classList.remove('d-none');
            overlay.classList.add('d-flex');
            btn.innerHTML = '<i class="bi bi-arrow-counterclockwise"></i>';
            btn.title = 'Deshacer';
            btn.style.backgroundColor = '#6c757d';
        } else {
            card.classList.remove('border-danger');
            card.style.opacity = '1';
            overlay.classList.remove('d-flex');
            overlay.classList.add('d-none');
            btn.innerHTML = '<i class="bi bi-x"></i>';
            btn.title = 'Marcar para eliminar';
            btn.style.backgroundColor = '';
        }
    };

    document.addEventListener('DOMContentLoaded', () => {
        if (typeof window.initImagePreview === 'function') {
            window.initImagePreview('attachments', 'file-preview-container');
        }
        if (typeof window.initImageViewer === 'function') {
            window.initImageViewer('modal-image-viewer', 'viewer-img', 'viewer-filename', 'btn-viewer-download');
        }
        if (typeof window.initReadMore === 'function') {
            window.initReadMore();
        }

        // Reopen modal with validation errors if the form submission failed
        @if ($errors->any() && old('form_id') === 'edit_project')
            const modal = new bootstrap.Modal(document.getElementById('modal-edit-project'));
            modal.show();
        @endif

        // Initialize previews for post edit modals
        @foreach ($project->posts as $post)
            if (typeof window.initImagePreview === 'function') {
                window.initImagePreview('attachments-{{ $post->id }}', 'file-preview-container-{{ $post->id }}');
            }
        @endforeach

        // Reopen post edit modal if validation failed for it
        @if ($errors->any() && str_starts_with(old('form_id'), 'edit_post_'))
            @php
                $failedPostId = str_replace('edit_post_', '', old('form_id'));
            @endphp
            const failedModalEl = document.getElementById('modal-edit-post-' + '{{ $failedPostId }}');
            if (failedModalEl) {
                const modal = new bootstrap.Modal(failedModalEl);
                modal.show();
            }
        @endif
    });
</script>
@endpush
