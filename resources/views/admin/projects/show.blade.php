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
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                                    <h3 class="h6 mb-0 fw-bold">{{ $post->title }}</h3>
                                    @if ($post->published_at)
                                        <span class="small text-muted text-nowrap">
                                            <i class="bi bi-calendar3 me-1"></i>
                                            {{ $post->published_at->format('d/m/Y H:i') }}
                                        </span>
                                    @endif
                                </div>
                                <div class="mb-3">
                                    <x-read-more :text="$post->description" class="small" />
                                </div>

                                @if ($post->attachments->count() > 0)
                                    <x-attachment-grid :attachments="$post->attachments" :postId="$post->id" />
                                @endif
                            </div>
                        </div>
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
    });
</script>
@endpush
