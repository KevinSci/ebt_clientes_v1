@extends('layouts.admin')

@section('title', 'Clientes')

@section('admin-content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h1 class="h3 fw-bold mb-0 text-dark">Clientes</h1>
        <p class="text-muted small mb-0">Gestión de cuentas de clientes</p>
    </div>
    <x-button
        variant="primary"
        icon="bi-plus-lg"
        data-bs-toggle="modal"
        data-bs-target="#modal-create-client"
        id="btn-open-create-client"
        class="fw-medium shadow-sm"
    >
        Nuevo Cliente
    </x-button>
</div>

{{-- ── Search bar ───────────────────────────────────────────────────────── --}}
<form method="GET" action="{{ route('admin.clients.index') }}" class="mb-4" role="search">
    <div class="input-group shadow-sm">
        <span class="input-group-text bg-white border-end-0 text-muted">
            <i class="bi bi-search" aria-hidden="true"></i>
        </span>
        <input
            type="search"
            name="search"
            id="search-clients"
            class="form-control border-start-0 ps-0"
            placeholder="Buscar por nombre, empresa o email…"
            value="{{ $search }}"
            aria-label="Buscar clientes"
        >
        <button type="submit" class="btn btn-primary fw-medium px-4">Buscar</button>
        @if ($search)
            <a href="{{ route('admin.clients.index') }}" class="btn btn-outline-secondary" aria-label="Limpiar búsqueda">
                <i class="bi bi-x-lg"></i>
            </a>
        @endif
    </div>
</form>

{{-- ── Clients grid ─────────────────────────────────────────────────────── --}}
@if ($clients->isEmpty())
    <x-alert type="info">
        No se encontraron clientes{{ $search ? " para «{$search}»" : '' }}.
    </x-alert>
@else
    <div class="row g-4" id="clients-grid">
        @foreach ($clients as $client)
            <div class="col-12 col-md-6 col-xl-4">
                <a href="{{ route('admin.clients.show', $client) }}"
                   class="text-decoration-none d-block h-100">
                    <x-card class="h-100 transition-transform hover-shadow">
                        <div class="d-flex align-items-start gap-3">
                            <span class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center fw-bold flex-shrink-0 shadow-sm" style="width: 52px; height: 52px; font-size: 1.3rem;">
                                {{ mb_strtoupper(substr($client->name, 0, 1)) }}
                            </span>
                            <div class="min-w-0 flex-grow-1">
                                <h2 class="h6 mb-0 fw-bold text-dark text-truncate transition-colors">
                                    {{ $client->name }}
                                </h2>
                                @if ($client->company_name)
                                    <p class="text-muted small mb-1 text-truncate">
                                        <i class="bi bi-building me-1"></i>{{ $client->company_name }}
                                    </p>
                                @endif
                                <p class="text-muted small mb-2 text-truncate">
                                    <i class="bi bi-envelope me-1"></i>{{ $client->email }}
                                </p>
                                @if ($client->phone)
                                    <p class="text-muted small mb-2">
                                        <i class="bi bi-telephone me-1"></i>{{ $client->phone }}
                                    </p>
                                @endif
                                <div class="d-flex align-items-center gap-2 mt-3 pt-2 border-top">
                                    <i class="bi bi-folder2-open text-primary small"></i>
                                    <span class="small text-muted fw-medium">
                                        {{ $client->projects_count }}
                                        {{ Str::plural('proyecto', $client->projects_count) }}
                                    </span>
                                    <span class="ms-auto text-primary">
                                        <i class="bi bi-arrow-right-circle-fill"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </x-card>
                </a>
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
        <x-button type="submit" form="form-create-client" variant="primary" icon="bi-person-plus" class="fw-medium shadow-sm">
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
<style>
    .hover-shadow { transition: box-shadow 0.2s ease, transform 0.2s ease; }
    .hover-shadow:hover { box-shadow: 0 4px 20px rgba(35, 38, 145, 0.1) !important; transform: translateY(-2px); }
    .transition-colors { transition: color 0.2s ease; }
    a:hover .transition-colors { color: var(--bs-primary) !important; }
</style>
@endpush
