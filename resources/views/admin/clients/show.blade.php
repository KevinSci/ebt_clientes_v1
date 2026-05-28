@extends('layouts.admin')

@section('title', $client->name . ' — Cliente')

@section('admin-content')

{{-- Breadcrumb --}}
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb bg-transparent p-0 m-0 small">
        <li class="breadcrumb-item">
            <a href="{{ route('admin.clients.index') }}" class="text-decoration-none">Clientes</a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">{{ $client->name }}</li>
    </ol>
</nav>

{{-- ── Client profile header ───────────────────────────────────────────── --}}
<x-card class="mb-4 bg-white border-0 shadow-sm rounded-4">
    <div class="d-flex flex-wrap align-items-center gap-4 p-2">
        <span class="rounded-circle bg-danger bg-gradient text-white d-flex align-items-center justify-content-center fw-bold shadow flex-shrink-0" style="width: 72px; height: 72px; font-size: 1.8rem;">
            {{ mb_strtoupper(substr($client->name, 0, 1)) }}
        </span>
        <div class="flex-grow-1">
            <h1 class="h4 mb-1 fw-bold text-dark">{{ $client->name }}</h1>
            @if ($client->company_name)
                <p class="mb-1 text-muted">
                    <i class="bi bi-building me-2"></i>{{ $client->company_name }}
                </p>
            @endif
            <p class="mb-1 text-muted small">
                <i class="bi bi-envelope me-2 text-primary"></i>{{ $client->email }}
            </p>
            @if ($client->phone)
                <p class="mb-0 text-muted small">
                    <i class="bi bi-telephone me-2 text-primary"></i>{{ $client->phone }}
                </p>
            @endif
        </div>
        <div class="text-center bg-light rounded-4 px-4 py-3 border border-secondary border-opacity-10">
            <p class="display-6 fw-bold text-primary mb-0">{{ $client->projects->count() }}</p>
            <p class="small text-muted mb-0 fw-medium text-uppercase letter-spacing-1">{{ Str::plural('Proyecto', $client->projects->count()) }}</p>
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
    <div class="row g-4">
        @foreach ($client->projects as $project)
            <div class="col-12 col-lg-6">
                <a href="{{ route('admin.clients.projects.show', [$client, $project]) }}"
                   class="text-decoration-none d-block h-100">
                    <x-card class="h-100 border-start border-4 border-primary hover-shadow transition-transform">
                        <div class="d-flex align-items-start justify-content-between gap-2 mb-3">
                            <h3 class="h6 mb-0 fw-bold text-dark">{{ $project->name }}</h3>
                            <x-badge :status="$project->status" />
                        </div>
                        <x-progress-bar :percentage="$project->progress_percentage" />
                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top border-light">
                            <span class="small text-muted">
                                <i class="bi bi-calendar3 me-1"></i>
                                {{ $project->created_at->format('d/m/Y') }}
                            </span>
                            <span class="small text-primary fw-semibold">
                                Ver detalles <i class="bi bi-arrow-right ms-1"></i>
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

@endsection

@push('scripts')
<script>
    // Reopen modal with validation errors if the form submission failed
    @if ($errors->any())
        document.addEventListener('DOMContentLoaded', function () {
            const modal = new bootstrap.Modal(document.getElementById('modal-create-project'));
            modal.show();
        });
    @endif
</script>
<style>
    .hover-shadow { transition: box-shadow 0.2s ease, transform 0.2s ease; border-left-color: var(--bs-primary) !important; }
    .hover-shadow:hover { box-shadow: 0 4px 20px rgba(35, 38, 145, 0.1) !important; transform: translateY(-2px); border-left-color: var(--bs-danger) !important; }
</style>
@endpush
