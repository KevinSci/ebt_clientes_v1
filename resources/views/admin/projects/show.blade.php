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
                </p>
                <div class="d-flex align-items-center gap-2 mt-1">
                    <x-badge :status="$project->status" />

                    <button type="button" class="btn btn-outline-secondary btn-sm"
                            data-bs-toggle="modal"
                            data-bs-target="#modal-edit-project"
                            id="btn-open-edit-project">
                        <i class="bi bi-pencil me-1"></i>Editar
                    </button>

                    <form action="{{ route('admin.companies.projects.destroy', [$company, $project]) }}" method="POST"
                          onsubmit="return confirm('¿Estás seguro de que deseas eliminar este proyecto y todas sus publicaciones de forma permanente?');"
                          class="d-inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm">
                            <i class="bi bi-trash3 me-1"></i>Eliminar
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
        <x-post-form :action="route('admin.companies.projects.posts.store', [$company, $project])" />
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
                        <x-post-card 
                            :post="$post" 
                            :canEdit="true"
                            editModalTarget="#modal-edit-post-global"
                            :editUpdateUrl="route('admin.companies.projects.posts.update', [$company, $project, $post])"
                            :editDeleteUrl="route('admin.companies.projects.posts.destroy', [$company, $project, $post])"
                        />
                    @endforeach
                </div>
            </x-scrollable>
        @endif
    </div>

</div>

<x-image-viewer-modal title="Vista de imagen" />
<x-folder-viewer-modal title="Contenido de Carpeta" />
<x-post-edit-modal-global />

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

{{-- Data attributes for JS module initialization (projectPageInit.js, modalReopen.js, folderUploader.js) --}}
<div id="project-page-init"
     data-post-ids="{{ $project->posts->pluck('id')->toJson() }}"
     data-ajax-store-url="{{ route('admin.companies.projects.posts.store-ajax', [$company, $project]) }}"
     data-ajax-update-url-template="{{ route('admin.companies.projects.posts.update-ajax', [$company, $project, '__POST_ID__']) }}"
     data-upload-url-template="{{ route('admin.companies.projects.posts.attachments.upload', [$company, $project, '__POST_ID__']) }}"
     data-redirect-url="{{ route('admin.companies.projects.show', [$company, $project]) }}">
</div>

@if ($errors->any())
    <div data-reopen-form-id="{{ old('form_id') }}"
         data-modal-map='{"edit_project":"modal-edit-project"}'
         data-reopen-post-prefix="edit_post_"
         data-reopen-post-modal-prefix="modal-edit-post-"></div>
@endif



@endsection
