@extends('layouts.admin')

@section('title', 'Clientes')

@section('admin-content')
<div class="ebt-page-header d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h1 class="ebt-page-header__title h4 mb-0">Clientes</h1>
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
    <div class="input-group ebt-search">
        <span class="input-group-text ebt-search__icon">
            <i class="bi bi-search" aria-hidden="true"></i>
        </span>
        <input
            type="search"
            name="search"
            id="search-clients"
            class="form-control ebt-search__input"
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
                   class="text-decoration-none ebt-client-card-link">
                    <x-card class="ebt-client-card h-100">
                        <div class="d-flex align-items-start gap-3">
                            <span class="ebt-avatar ebt-avatar--lg flex-shrink-0">
                                {{ mb_strtoupper(substr($client->name, 0, 1)) }}
                            </span>
                            <div class="min-w-0 flex-grow-1">
                                <h2 class="h6 mb-0 fw-bold text-truncate ebt-client-card__name">
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
                                <div class="d-flex align-items-center gap-2 mt-2">
                                    <i class="bi bi-folder2-open text-primary small"></i>
                                    <span class="small text-muted">
                                        {{ $client->projects_count }}
                                        {{ Str::plural('proyecto', $client->projects_count) }}
                                    </span>
                                    <span class="ms-auto">
                                        <i class="bi bi-arrow-right-circle text-primary"></i>
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
