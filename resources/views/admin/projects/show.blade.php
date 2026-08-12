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

                    @if ($project->isPhasesProgress())
                        <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle rounded-pill px-3 py-2 text-uppercase fw-semibold">
                            <i class="bi bi-diagram-3 me-1"></i>Fases y Tareas
                        </span>
                    @else
                        <span class="badge bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle rounded-pill px-3 py-2 text-uppercase fw-semibold">
                            <i class="bi bi-sliders me-1"></i>Avance Manual
                        </span>
                    @endif

                    <button type="button" class="btn btn-outline-secondary btn-sm ms-1"
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

{{-- ── Phases & Tasks Section (If project is in phases mode) ───────────── --}}
@if ($project->isPhasesProgress())
    <div class="card mb-4">
        <div class="card-header bg-light d-flex align-items-center justify-content-between py-3">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-diagram-3-fill text-primary fs-5"></i>
                <h2 class="h5 mb-0 fw-bold">Fases y Tareas del Proyecto</h2>
                <span class="badge bg-primary rounded-pill">{{ $project->phases->count() }} Fases</span>
            </div>
            <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-1"
                    data-bs-toggle="modal" data-bs-target="#modal-create-phase">
                <i class="bi bi-plus-lg"></i>
                <span>Nueva Fase</span>
            </button>
        </div>
        <div class="card-body">
            @if ($project->phases->isEmpty())
                <x-alert type="info">
                    No se han definido fases en este proyecto aún. Haz clic en "Nueva Fase" para comenzar.
                </x-alert>
            @else
                <div class="accordion" id="phasesAccordion">
                    @foreach ($project->phases as $phase)
                        @php
                            $phasePercentage = $phase->tasks_count > 0 
                                ? (int) round(($phase->completed_tasks_count / $phase->tasks_count) * 100) 
                                : 0;
                            $phaseBadgeClass = match($phase->status) {
                                'completed' => 'bg-success-subtle text-success-emphasis border-success-subtle',
                                'in_progress' => 'bg-primary-subtle text-primary-emphasis border-primary-subtle',
                                default => 'bg-secondary-subtle text-secondary-emphasis border-secondary-subtle',
                            };
                            $phaseStatusLabel = match($phase->status) {
                                'completed' => 'Completada',
                                'in_progress' => 'En Progreso',
                                default => 'Pendiente',
                            };
                        @endphp
                        <div class="accordion-item border rounded mb-3 overflow-hidden shadow-sm">
                            <div class="accordion-header" id="heading-phase-{{ $phase->id }}">
                                <div class="accordion-button bg-light py-2 px-3 {{ $loop->first ? '' : 'collapsed' }}"
                                     type="button" data-bs-toggle="collapse"
                                     data-bs-target="#collapse-phase-{{ $phase->id }}"
                                     aria-expanded="{{ $loop->first ? 'true' : 'false' }}"
                                     aria-controls="collapse-phase-{{ $phase->id }}">
                                    <div class="d-flex flex-wrap align-items-center justify-content-between w-100 me-3 gap-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="fw-bold text-dark h6 mb-0">{{ $phase->name }}</span>
                                            <span class="badge border {{ $phaseBadgeClass }} rounded-pill small">
                                                {{ $phaseStatusLabel }}
                                            </span>
                                        </div>
                                        <div class="d-flex align-items-center gap-3">
                                            <span class="small text-muted fw-medium">
                                                {{ $phase->completed_tasks_count }}/{{ $phase->tasks_count }} tareas ({{ $phasePercentage }}%)
                                            </span>
                                            <div class="progress me-2" style="width: 80px; height: 6px;">
                                                <div class="progress-bar bg-{{ $phase->status === 'completed' ? 'success' : 'primary' }}" 
                                                     style="width: {{ $phasePercentage }}%"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="collapse-phase-{{ $phase->id }}"
                                 class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}"
                                 aria-labelledby="heading-phase-{{ $phase->id }}"
                                 data-bs-parent="#phasesAccordion">
                                <div class="accordion-body p-3">
                                    {{-- Actions for phase --}}
                                    <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                                        <button type="button" class="btn btn-outline-primary btn-sm d-flex align-items-center gap-1"
                                                data-bs-toggle="modal" data-bs-target="#modal-create-task-{{ $phase->id }}">
                                            <i class="bi bi-plus-circle"></i> Agregar Tarea
                                        </button>
                                        <div class="d-flex gap-1">
                                            <button type="button" class="btn btn-link btn-sm text-secondary p-0 me-2"
                                                    data-bs-toggle="modal" data-bs-target="#modal-edit-phase-{{ $phase->id }}">
                                                <i class="bi bi-pencil me-1"></i>Editar Fase
                                            </button>
                                            <form action="{{ route('admin.companies.projects.phases.destroy', [$company, $project, $phase]) }}"
                                                  method="POST" onsubmit="return confirm('¿Eliminar esta fase y todas sus tareas?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-link btn-sm text-danger p-0">
                                                    <i class="bi bi-trash me-1"></i>Eliminar Fase
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    {{-- Tasks list --}}
                                    @if ($phase->tasks->isEmpty())
                                        <p class="text-muted small italic mb-0">No hay tareas en esta fase.</p>
                                    @else
                                        <div class="list-group list-group-flush">
                                            @foreach ($phase->tasks as $task)
                                                <div class="list-group-item d-flex align-items-center justify-content-between py-2 px-1 border-0">
                                                    <div class="d-flex align-items-center gap-3 min-w-0">
                                                        <form action="{{ route('admin.companies.projects.phases.tasks.toggle', [$company, $project, $phase, $task]) }}" 
                                                              method="POST" class="m-0">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="completed" value="{{ $task->is_completed ? '0' : '1' }}">
                                                            <input type="checkbox" class="form-check-input fs-5 cursor-pointer"
                                                                   onchange="this.form.submit()"
                                                                   {{ $task->is_completed ? 'checked' : '' }}
                                                                   title="{{ $task->is_completed ? 'Desmarcar tarea' : 'Marcar como completada' }}">
                                                        </form>
                                                        <span class="text-break {{ $task->is_completed ? 'text-decoration-line-through text-muted' : 'fw-medium text-dark' }}">
                                                            {{ $task->name }}
                                                        </span>
                                                        @if ($task->is_completed && $task->completed_at)
                                                            <span class="badge bg-success-subtle text-success small border border-success-subtle">
                                                                <i class="bi bi-check-lg me-1"></i>{{ $task->completed_at->format('d/m/Y H:i') }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <form action="{{ route('admin.companies.projects.phases.tasks.destroy', [$company, $project, $phase, $task]) }}"
                                                          method="POST" onsubmit="return confirm('¿Eliminar esta tarea?');" class="m-0 ms-2">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger btn-sm py-0 px-2 border-0" title="Eliminar tarea">
                                                            <i class="bi bi-x-lg"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endif

<div class="row g-4">

    {{-- ── Left: Post form ──────────────────────────────────────────────── --}}
    <div class="col-12 col-xl-5">
        <x-post-form :action="route('admin.companies.projects.posts.store', [$company, $project])" :project="$project" />
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
            @if ($project->isManualProgress())
                <div class="col-12 col-md-6">
                    <x-input name="progress_percentage" type="number" label="Porcentaje de avance" :required="true"
                             placeholder="0 - 100" min="0" max="100" :value="$project->progress_percentage" />
                </div>
            @else
                <div class="col-12 col-md-6">
                    <label class="form-label fw-medium text-muted">Porcentaje de avance</label>
                    <input type="text" class="form-control bg-light" value="{{ $project->progress_percentage }}% (Automático)" readonly disabled>
                </div>
            @endif
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

{{-- Modals for Phase and Task Management --}}
@if ($project->isPhasesProgress())
    {{-- Modal: Create Phase --}}
    <x-modal id="modal-create-phase" title="Nueva Fase" size="md">
        <form method="POST" action="{{ route('admin.companies.projects.phases.store', [$company, $project]) }}" id="form-create-phase">
            @csrf
            <x-input name="name" label="Nombre de la Fase" :required="true" placeholder="Ej. Fase 1: Planeación y Análisis" />
        </form>
        <x-slot:footer>
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
            <x-button type="submit" form="form-create-phase" variant="primary" icon="bi-plus-lg">Crear Fase</x-button>
        </x-slot:footer>
    </x-modal>

    @foreach ($project->phases as $phase)
        {{-- Modal: Edit Phase --}}
        <x-modal id="modal-edit-phase-{{ $phase->id }}" title="Editar Fase" size="md">
            <form method="POST" action="{{ route('admin.companies.projects.phases.update', [$company, $project, $phase]) }}" id="form-edit-phase-{{ $phase->id }}">
                @csrf
                @method('PUT')
                <x-input name="name" label="Nombre de la Fase" :required="true" :value="$phase->name" />
            </form>
            <x-slot:footer>
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <x-button type="submit" form="form-edit-phase-{{ $phase->id }}" variant="primary" icon="bi-check-lg">Guardar Cambios</x-button>
            </x-slot:footer>
        </x-modal>

        {{-- Modal: Create Task --}}
        <x-modal id="modal-create-task-{{ $phase->id }}" title="Nueva Tarea - {{ $phase->name }}" size="md">
            <form method="POST" action="{{ route('admin.companies.projects.phases.tasks.store', [$company, $project, $phase]) }}" id="form-create-task-{{ $phase->id }}">
                @csrf
                <x-input name="name" label="Nombre de la Tarea" :required="true" placeholder="Ej. Revisar entregables iniciales" />
            </form>
            <x-slot:footer>
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <x-button type="submit" form="form-create-task-{{ $phase->id }}" variant="primary" icon="bi-plus-lg">Agregar Tarea</x-button>
            </x-slot:footer>
        </x-modal>
    @endforeach
@endif

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
