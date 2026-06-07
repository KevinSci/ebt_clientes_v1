@extends('layouts.admin')

@section('title', $client->name . ' — Cliente')

@section('admin-content')

{{-- Breadcrumb --}}
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb ebt-breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('admin.clients.index') }}" class="text-decoration-none">Clientes</a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">{{ $client->name }}</li>
    </ol>
</nav>

{{-- ── Client profile header ───────────────────────────────────────────── --}}
<x-card class="mb-4 ebt-profile-card">
    <div class="d-flex flex-wrap align-items-center gap-4">
        <span class="ebt-avatar ebt-avatar--xl flex-shrink-0">
            <i class="bi bi-person-fill"></i>
        </span>
        <div class="flex-grow-1">
            <h1 class="h4 mb-1 fw-bold">{{ $client->name }}</h1>
            @if ($client->company_name)
                <p class="mb-1 text-muted">
                    <i class="bi bi-building me-2"></i>{{ $client->company_name }}
                </p>
            @endif
            <p class="mb-1 text-muted small">
                <i class="bi bi-envelope me-2"></i>{{ $client->email }}
            </p>
            @if ($client->phone)
                <p class="mb-0 text-muted small">
                    <i class="bi bi-telephone me-2"></i>{{ $client->phone }}
                </p>
            @endif
        </div>
        <div class="text-center ebt-profile-card__stat">
            <p class="display-6 fw-bold text-primary mb-0">{{ $client->projects->count() }}</p>
            <p class="small text-muted mb-0">{{ Str::plural('Proyecto', $client->projects->count()) }}</p>
        </div>
    </div>
</x-card>

{{-- ── Projects list ───────────────────────────────────────────────────── --}}
<div class="d-flex align-items-center justify-content-between mb-4 mt-5 border-bottom pb-2">
    <h2 class="h5 mb-0 fw-bold text-dark">Proyectos del Cliente</h2>
    <x-button
        variant="primary"
        icon="bi-plus-lg"
        data-bs-toggle="modal"
        data-bs-target="#modal-create-project"
        id="btn-open-create-project"
        class="shadow-sm fw-medium"
    >
        Nuevo Proyecto
    </x-button>
</div>

@if ($client->projects->isEmpty())
    <x-alert type="info" class="shadow-sm border-0 bg-white">Este cliente no tiene proyectos aún.</x-alert>
@else
    <div class="row g-3">
        @foreach ($client->projects as $project)
            <div class="col-12 col-lg-6">
                <a href="{{ route('admin.clients.projects.show', [$client, $project]) }}"
                   class="text-decoration-none ebt-project-card-link">
                    <x-card class="ebt-project-card h-100">
                        <div class="d-flex align-items-start justify-content-between gap-2 mb-3">
                            <h3 class="h6 mb-0 fw-semibold">{{ $project->name }}</h3>
                            <x-badge :status="$project->status" />
                        </div>
                        <x-progress-bar :percentage="$project->progress_percentage" />
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <span class="small text-muted">
                                <i class="bi bi-calendar3 me-1"></i>
                                {{ $project->created_at->format('d/m/Y') }}
                            </span>
                            <span class="small text-primary fw-medium">
                                Ver proyecto <i class="bi bi-arrow-right ms-1"></i>
                            </span>
                        </div>
                    </x-card>
                </a>
            </div>
        @endforeach
    </div>
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
                <div class="mb-3">
                    <label for="status" class="form-label fw-medium text-dark">
                        Estado <span class="text-danger ms-1" aria-hidden="true">*</span>
                    </label>
                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                        <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Activo</option>
                        <option value="paused" {{ old('status') === 'paused' ? 'selected' : '' }}>Pausado</option>
                        <option value="completed" {{ old('status') === 'completed' ? 'selected' : '' }}>Completado</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-12 col-md-6">
                <x-input name="progress_percentage" type="number" label="Porcentaje de avance" :required="true"
                         placeholder="0 - 100" min="0" max="100" value="0" />
            </div>
        </div>

    </form>

    <x-slot:footer>
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
            Cancelar
        </button>
        <x-button type="submit" form="form-create-project" variant="primary" icon="bi-plus-lg" class="fw-medium shadow-sm">
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

@endsection

@push('scripts')
<script>
    // Reopen modal with validation errors if the form submission failed
    @if ($errors->any())
        document.addEventListener('DOMContentLoaded', function () {
            const formId = "{{ old('form_id') }}";
            if (formId === 'edit_client') {
                const modal = new bootstrap.Modal(document.getElementById('modal-edit-client'));
                modal.show();
            } else if (formId === 'create_project') {
                const modal = new bootstrap.Modal(document.getElementById('modal-create-project'));
                modal.show();
            }
        });
    @endif
</script>
<style>
    .hover-shadow { transition: box-shadow 0.2s ease, transform 0.2s ease; border-left-color: var(--bs-primary) !important; }
    .hover-shadow:hover { box-shadow: 0 4px 20px rgba(35, 38, 145, 0.1) !important; transform: translateY(-2px); border-left-color: var(--bs-danger) !important; }
</style>
@endpush
