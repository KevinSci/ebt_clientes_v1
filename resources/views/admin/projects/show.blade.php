@extends('layouts.admin')

@section('title', $project->name)

@section('admin-content')

{{-- Breadcrumb --}}
<x-breadcrumb :items="[
    ['label' => 'Empresas', 'url' => route('admin.companies.index')],
    ['label' => $company->name, 'url' => route('admin.companies.show', $company)],
    ['label' => $project->name],
]" />

{{-- ── Project header ───────────────────────────────────────────────────── --}}
<div class="card mb-4" id="project-header">
    <div class="card-body">
        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3">
            <div>
                <h1 class="h4 mb-1 fw-bold">{{ $project->name }}</h1>
                <p class="text-muted small mb-2">
                    Empresa: <strong>{{ $company->name }}</strong>
                    @if ($company->rfc) — RFC: {{ $company->rfc }} @endif
                </p>
                <div class="d-flex align-items-center gap-2 mt-1">
                    <x-badge :status="$project->status" />

                    <button type="button" class="btn btn-outline-secondary btn-sm"
                            data-bs-toggle="modal"
                            data-bs-target="#modal-edit-project"
                            id="btn-open-edit-project">
                        <i class="bi bi-pencil me-1"></i>Editar Proyecto
                    </button>

                    <form action="{{ route('admin.companies.projects.destroy', [$company, $project]) }}" method="POST"
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
                      action="{{ route('admin.companies.projects.posts.store', [$company, $project]) }}"
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

                    <x-textarea
                        name="description"
                        label="Descripción"
                        :required="true"
                        placeholder="Describe el avance, observaciones o hallazgos del proyecto…"
                    />

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
                            accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.zip,.rar"
                        >
                        <div class="form-text">
                            Imágenes (JPG, PNG, GIF, WebP), PDFs, Word, Excel y archivos comprimidos (ZIP, RAR). Máx. 20 MB por archivo.
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
                                        <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2 ebt-btn-xs"
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
                            <form method="POST" action="{{ route('admin.companies.projects.posts.update', [$company, $project, $post]) }}" 
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

                                                        <!-- Overlay indicator to show marked for deletion -->
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
                                    <input
                                        type="file"
                                        id="attachments-{{ $post->id }}"
                                        name="attachments[]"
                                        class="form-control"
                                        multiple
                                        accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.zip,.rar"
                                    >
                                    <div class="form-text">
                                        Imágenes (JPG, PNG, GIF, WebP), PDFs, Word, Excel y archivos comprimidos (ZIP, RAR). Máx. 20 MB por archivo.
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
    <form method="POST" action="{{ route('admin.companies.projects.update', [$company, $project]) }}" id="form-edit-project" novalidate>
        @csrf
        @method('PUT')
        <input type="hidden" name="form_id" value="edit_project">

        <div class="row g-3">
            <div class="col-12">
                <x-input name="name" label="Nombre del proyecto" :required="true" placeholder="Ej. Implementación Fase 1" :value="$project->name" />
            </div>
            <div class="col-12 col-md-6">
                <x-status-select name="status" :value="$project->status" />
            </div>
            <div class="col-12 col-md-6">
                <x-input name="progress_percentage" type="number" label="Porcentaje de avance" :required="true"
                         placeholder="0 - 100" min="0" max="100" :value="$project->progress_percentage" />
            </div>
            <div class="col-12">
                <x-input name="created_at" type="datetime-local" label="Fecha de creación"
                         :value="$project->created_at ? $project->created_at->format('Y-m-d\TH:i') : ''" />
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

{{-- Data attributes for JS module initialization (projectPageInit.js, modalReopen.js) --}}
<div id="project-page-init" data-post-ids="{{ $project->posts->pluck('id')->toJson() }}"></div>

@if ($errors->any())
    <div data-reopen-form-id="{{ old('form_id') }}"
         data-modal-map='{"edit_project":"modal-edit-project"}'
         data-reopen-post-prefix="edit_post_"
         data-reopen-post-modal-prefix="modal-edit-post-"></div>
@endif

@endsection
