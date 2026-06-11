@extends('layouts.admin')

@section('title', $client->name)

@section('admin-content')

{{-- Breadcrumb --}}
<x-breadcrumb :items="[
    ['label' => 'Clientes', 'url' => route('admin.clients.index')],
    ['label' => $client->name],
]" />

{{-- ── Client profile header ───────────────────────────────────────────── --}}
<div class="card mb-4" id="client-info-card">
    <div class="card-body">
        <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-3 justify-content-between">
            <div class="d-flex align-items-center gap-3 flex-grow-1 min-w-0 w-100 w-md-auto">
                <x-avatar size="lg" variant="primary" icon="bi-person-fill" />
                <div class="min-w-0 flex-grow-1">
                    <h1 class="h4 mb-1 fw-bold text-break">{{ $client->name }}</h1>
                    @if ($client->company_name)
                        <p class="mb-1 text-muted small text-break d-flex align-items-start gap-2">
                            <i class="bi bi-building mt-1 flex-shrink-0" aria-hidden="true"></i>
                            <span class="text-break">{{ $client->company_name }}</span>
                        </p>
                    @endif
                    <p class="mb-1 text-muted small text-break d-flex align-items-start gap-2">
                        <i class="bi bi-envelope mt-1 flex-shrink-0" aria-hidden="true"></i>
                        <span class="text-break">{{ $client->email }}</span>
                    </p>
                    @if ($client->phone)
                        <p class="mb-0 text-muted small text-break d-flex align-items-start gap-2">
                            <i class="bi bi-telephone mt-1 flex-shrink-0" aria-hidden="true"></i>
                            <span class="text-break">{{ $client->phone }}</span>
                        </p>
                    @endif
                </div>
            </div>
            <div class="d-flex align-items-center justify-content-between justify-content-md-end gap-4 w-100 w-md-auto mt-3 mt-md-0">
                <div class="text-center flex-shrink-0">
                    <p class="h3 fw-bold text-primary mb-0">{{ $client->projects->count() }}</p>
                    <p class="small text-muted mb-0">{{ Str::plural('Proyecto', $client->projects->count()) }}</p>
                </div>
                <div class="d-flex gap-2 flex-shrink-0">
                    <button class="btn btn-outline-secondary d-flex align-items-center gap-2"
                            data-bs-toggle="modal"
                            data-bs-target="#modal-edit-client"
                            id="btn-open-edit-client">
                        <i class="bi bi-pencil"></i> <span class="d-none d-sm-inline">Editar</span>
                    </button>
                    <button class="btn btn-outline-danger d-flex align-items-center gap-2"
                            data-bs-toggle="modal"
                            data-bs-target="#modal-delete-client"
                            id="btn-open-delete-client">
                        <i class="bi bi-trash"></i> <span class="d-none d-sm-inline">Eliminar</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── Projects list ───────────────────────────────────────────────────── --}}
<div class="d-flex align-items-center justify-content-between mb-3">
    <h2 class="h5 mb-0">Proyectos</h2>
    <x-button
        variant="primary"
        icon="bi-plus-lg"
        data-bs-toggle="modal"
        data-bs-target="#modal-create-project"
        id="btn-open-create-project"
    >
        Nuevo Proyecto
    </x-button>
</div>

@if ($client->projects->isEmpty())
    <x-alert type="info">Este cliente no tiene proyectos aún.</x-alert>
@else
    <x-scrollable maxHeight="500px">
        <div class="d-flex flex-column gap-3 p-1">
            @foreach ($client->projects as $project)
                <x-project-card 
                    :project="$project" 
                    :href="route('admin.clients.projects.show', [$client, $project])" 
                    linkText="Ver proyecto" 
                    :historical="$project->status !== 'active'" 
                />
            @endforeach
        </div>
    </x-scrollable>
@endif

{{-- ══════════════════════════════════════════════════════════════════════ --}}
{{-- Modal: Create new project                                             --}}
{{-- ══════════════════════════════════════════════════════════════════════ --}}
<x-modal id="modal-create-project" title="Nuevo Proyecto" size="md">

    <form method="POST" action="{{ route('admin.clients.projects.store', $client) }}" id="form-create-project" novalidate>
        @csrf
        <input type="hidden" name="form_id" value="create_project">

        <div class="row g-3">
            <div class="col-12">
                <x-input name="name" label="Nombre del proyecto" :required="true" placeholder="Ej. Implementación Fase 1" />
            </div>
            <div class="col-12 col-md-6">
                <x-status-select name="status" value="active" />
            </div>
            <div class="col-12 col-md-6">
                <x-input name="progress_percentage" type="number" label="Porcentaje de avance" :required="true"
                         placeholder="0 - 100" min="0" max="100" value="0" />
            </div>
            <div class="col-12">
                <x-input name="created_at" type="datetime-local" label="Fecha de creación"
                         :value="now()->format('Y-m-d\TH:i')" />
            </div>
        </div>

    </form>

    <x-slot:footer>
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
            Cancelar
        </button>
        <x-button type="submit" form="form-create-project" variant="primary" icon="bi-plus-lg">
            Crear Proyecto
        </x-button>
    </x-slot:footer>

</x-modal>

{{-- ══════════════════════════════════════════════════════════════════════ --}}
{{-- Modal: Edit client                                                    --}}
{{-- ══════════════════════════════════════════════════════════════════════ --}}
<x-modal id="modal-edit-client" title="Editar Cliente" size="lg">

    <form method="POST" action="{{ route('admin.clients.update', $client) }}" id="form-edit-client" novalidate>
        @csrf
        @method('PUT')

        <input type="hidden" name="form_id" value="edit_client">

        <div class="row g-3">
            <div class="col-12 col-md-6">
                <x-input name="name" label="Nombre completo" :required="true" placeholder="Juan Pérez" :value="$client->name" />
            </div>
            <div class="col-12 col-md-6">
                <x-input name="company_name" label="Empresa" placeholder="Empresa S.A. de C.V." :value="$client->company_name" />
            </div>
            <div class="col-12 col-md-6">
                <x-input name="email" type="email" label="Correo electrónico" :required="true"
                         placeholder="juan@empresa.com" :value="$client->email" />
            </div>
            <div class="col-12 col-md-6">
                <x-input name="phone" label="Teléfono" placeholder="+52 55 0000 0000" :value="$client->phone" />
            </div>
            <div class="col-12 col-md-6">
                <x-input name="password" type="password" label="Nueva contraseña"
                         placeholder="Dejar en blanco para conservar" />
            </div>
            <div class="col-12 col-md-6">
                <x-input name="password_confirmation" type="password" label="Confirmar nueva contraseña"
                         placeholder="Repite la contraseña" />
            </div>
        </div>

    </form>

    <x-slot:footer>
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
            Cancelar
        </button>
        <x-button type="submit" form="form-edit-client" variant="primary" icon="bi-check-lg">
            Guardar Cambios
        </x-button>
    </x-slot:footer>

</x-modal>

{{-- ══════════════════════════════════════════════════════════════════════ --}}
{{-- Modal: Delete client                                                  --}}
{{-- ══════════════════════════════════════════════════════════════════════ --}}
<x-modal id="modal-delete-client" title="Eliminar Cliente" size="md">

    <form method="POST" action="{{ route('admin.clients.destroy', $client) }}" id="form-delete-client">
        @csrf
        @method('DELETE')

        <div class="text-center my-3">
            <i class="bi bi-exclamation-triangle text-danger fs-1"></i>
            <h3 class="h5 mt-3 fw-bold">¿Estás seguro de que deseas eliminar a este cliente?</h3>
            <p class="text-muted small px-3">
                Esta acción es irreversible y también eliminará de forma lógica todos los proyectos asociados a <strong>{{ $client->name }}</strong>.
            </p>
        </div>

    </form>

    <x-slot:footer>
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
            Cancelar
        </button>
        <x-button type="submit" form="form-delete-client" variant="danger" icon="bi-trash">
            Eliminar Cliente
        </x-button>
    </x-slot:footer>

</x-modal>

@if ($errors->any())
    <div data-reopen-form-id="{{ old('form_id') }}"
         data-modal-map='{"edit_client":"modal-edit-client","create_project":"modal-create-project"}'></div>
@endif

@endsection
