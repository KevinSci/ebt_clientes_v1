@extends('layouts.admin')

@section('title', 'Empresas')

@section('admin-content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h1 class="h4 mb-0">Empresas</h1>
        <p class="text-muted small mb-0">Gestión de empresas B2B y proyectos</p>
    </div>
    <x-button
        variant="primary"
        icon="bi-building-fill-add"
        data-bs-toggle="modal"
        data-bs-target="#modal-create-company"
        id="btn-open-create-company"
    >
        Nueva Empresa
    </x-button>
</div>

{{-- ── Search bar ───────────────────────────────────────────────────────── --}}
<form method="GET" action="{{ route('admin.companies.index') }}" class="mb-4" role="search">
    <div class="input-group">
        <span class="input-group-text">
            <i class="bi bi-search" aria-hidden="true"></i>
        </span>
        <input
            type="search"
            name="search"
            id="search-companies"
            class="form-control"
            placeholder="Buscar por nombre, RFC o teléfono…"
            value="{{ $search }}"
            aria-label="Buscar empresas"
        >
        <button type="submit" class="btn btn-primary">Buscar</button>
        @if ($search)
            <a href="{{ route('admin.companies.index') }}" class="btn btn-outline-secondary" aria-label="Limpiar búsqueda">
                <i class="bi bi-x-lg"></i>
            </a>
        @endif
    </div>
</form>

{{-- ── Companies cards ─────────────────────────────────────────────────── --}}
@if ($companies->isEmpty())
    <x-alert type="info">
        No se encontraron empresas{{ $search ? " para «{$search}»" : '' }}.
    </x-alert>
@else
    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-3 g-md-4" id="companies-grid">
        @foreach ($companies as $company)
            <div class="col">
                <x-company-card :company="$company" :url="route('admin.companies.show', $company)" />
            </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    <x-pagination :items="$companies" />
@endif

{{-- ══════════════════════════════════════════════════════════════════════ --}}
{{-- Modal: Create new company                                             --}}
{{-- ══════════════════════════════════════════════════════════════════════ --}}
<x-modal id="modal-create-company" title="Nueva Empresa" size="lg">

    <form method="POST" action="{{ route('admin.companies.store') }}" id="form-create-company" novalidate>
        @csrf

        <div class="row g-3">
            <div class="col-12 col-md-6">
                <x-input name="name" label="Nombre de la empresa" :required="true" placeholder="Empresa S.A. de C.V." />
            </div>
            <div class="col-12 col-md-6">
                <x-input name="rfc" label="RFC" placeholder="RFC de 12 o 13 caracteres" />
            </div>
            <div class="col-12 col-md-6">
                <div class="mb-3">
                    <label for="tax_regime" class="form-label fw-semibold">Régimen Fiscal <span class="text-danger">*</span></label>
                    <select name="tax_regime" id="tax_regime" class="form-select @error('tax_regime') is-invalid @enderror" required>
                        <option value="moral" {{ old('tax_regime') === 'moral' || !old('tax_regime') ? 'selected' : '' }}>Persona Moral</option>
                        <option value="fisica" {{ old('tax_regime') === 'fisica' ? 'selected' : '' }}>Persona Física</option>
                    </select>
                    @error('tax_regime')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-12 col-md-6">
                <x-input name="phone" label="Teléfono" placeholder="+52 55 0000 0000" />
            </div>
            <div class="col-12">
                <x-input name="address" label="Dirección física" placeholder="Av. Paseo de la Reforma #123, Col. Centro" />
            </div>
        </div>

    </form>

    <x-slot:footer>
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
            Cancelar
        </button>
        <x-button type="submit" form="form-create-company" variant="primary" icon="bi-building-add">
            Crear Empresa
        </x-button>
    </x-slot:footer>

</x-modal>

@if ($errors->any())
    <div data-reopen-modal-id="modal-create-company"></div>
@endif

@endsection
