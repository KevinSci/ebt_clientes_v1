@extends('layouts.admin')

@section('title', 'Clientes')

@section('admin-content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h1 class="h4 mb-0">Clientes</h1>
        <p class="text-muted small mb-0">Gestión de cuentas de clientes</p>
    </div>
    <x-button
        variant="primary"
        icon="bi-plus-lg"
        data-bs-toggle="modal"
        data-bs-target="#modal-create-client"
        id="btn-open-create-client"
    >
        Nuevo Cliente
    </x-button>
</div>

{{-- ── Search bar ───────────────────────────────────────────────────────── --}}
<form method="GET" action="{{ route('admin.clients.index') }}" class="mb-4" role="search">
    <div class="input-group">
        <span class="input-group-text">
            <i class="bi bi-search" aria-hidden="true"></i>
        </span>
        <input
            type="search"
            name="search"
            id="search-clients"
            class="form-control"
            placeholder="Buscar por nombre, empresa o email…"
            value="{{ $search }}"
            aria-label="Buscar clientes"
        >
        <button type="submit" class="btn btn-primary">Buscar</button>
        @if ($search)
            <a href="{{ route('admin.clients.index') }}" class="btn btn-outline-secondary" aria-label="Limpiar búsqueda">
                <i class="bi bi-x-lg"></i>
            </a>
        @endif
    </div>
</form>

{{-- ── Clients cards ────────────────────────────────────────────────────── --}}
@if ($clients->isEmpty())
    <x-alert type="info">
        No se encontraron clientes{{ $search ? " para «{$search}»" : '' }}.
    </x-alert>
@else
    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4" id="clients-grid">
        @foreach ($clients as $client)
            <div class="col">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <span class="badge rounded-circle text-bg-primary d-inline-flex align-items-center justify-content-center flex-shrink-0 me-3" style="width: 48px; height: 48px; font-size: 1.25rem;">
                                {{ mb_strtoupper(substr($client->name, 0, 1)) }}
                            </span>
                            <div class="min-w-0">
                                <h5 class="card-title mb-0 fw-bold text-truncate">
                                    <a href="{{ route('admin.clients.show', $client) }}" class="text-decoration-none text-dark stretched-link">
                                        {{ $client->name }}
                                    </a>
                                </h5>
                                @if ($client->company_name)
                                    <h6 class="card-subtitle mt-1 text-muted small text-truncate">
                                        <i class="bi bi-building me-1"></i>{{ $client->company_name }}
                                    </h6>
                                @endif
                            </div>
                        </div>
                        
                        <p class="card-text small text-muted mb-1 text-truncate">
                            <i class="bi bi-envelope me-2"></i>{{ $client->email }}
                        </p>
                        @if ($client->phone)
                            <p class="card-text small text-muted mb-3">
                                <i class="bi bi-telephone me-2"></i>{{ $client->phone }}
                            </p>
                        @endif
                    </div>
                    <div class="card-footer bg-transparent border-top-0 pt-0 d-flex justify-content-between align-items-center">
                        <span class="badge text-bg-light border text-secondary">
                            <i class="bi bi-folder2-open me-1"></i>
                            {{ $client->projects_count }} {{ Str::plural('proyecto', $client->projects_count) }}
                        </span>
                        <i class="bi bi-arrow-right-circle text-primary fs-5" aria-hidden="true"></i>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    @if ($clients->hasPages())
        <div class="mt-4 d-flex justify-content-center">
            {{ $clients->links() }}
        </div>
    @endif
@endif

{{-- ══════════════════════════════════════════════════════════════════════ --}}
{{-- Modal: Create new client                                              --}}
{{-- ══════════════════════════════════════════════════════════════════════ --}}
<x-modal id="modal-create-client" title="Nuevo Cliente" size="lg">

    <form method="POST" action="{{ route('admin.clients.store') }}" id="form-create-client" novalidate>
        @csrf

        <div class="row g-3">
            <div class="col-12 col-md-6">
                <x-input name="name" label="Nombre completo" :required="true" placeholder="Juan Pérez" />
            </div>
            <div class="col-12 col-md-6">
                <x-input name="company_name" label="Empresa" placeholder="Empresa S.A. de C.V." />
            </div>
            <div class="col-12 col-md-6">
                <x-input name="email" type="email" label="Correo electrónico" :required="true"
                         placeholder="juan@empresa.com" />
            </div>
            <div class="col-12 col-md-6">
                <x-input name="phone" label="Teléfono" placeholder="+52 55 0000 0000" />
            </div>
            <div class="col-12 col-md-6">
                <x-input name="password" type="password" label="Contraseña" :required="true"
                         placeholder="Mínimo 8 caracteres" />
            </div>
            <div class="col-12 col-md-6">
                <x-input name="password_confirmation" type="password" label="Confirmar contraseña"
                         :required="true" placeholder="Repite la contraseña" />
            </div>
        </div>

    </form>

    <x-slot:footer>
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
            Cancelar
        </button>
        <x-button type="submit" form="form-create-client" variant="primary" icon="bi-person-plus">
            Crear Cliente
        </x-button>
    </x-slot:footer>

</x-modal>

@endsection

@push('scripts')
<script>
    // Re-open modal with validation errors if the form submission failed
    @if ($errors->any())
        document.addEventListener('DOMContentLoaded', function () {
            const modal = new bootstrap.Modal(document.getElementById('modal-create-client'));
            modal.show();
        });
    @endif
</script>
@endpush
