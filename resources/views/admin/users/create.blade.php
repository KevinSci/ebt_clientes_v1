@extends('layouts.admin')

@section('title', 'Nuevo Usuario')

@section('admin-content')

{{-- Breadcrumb --}}
<x-breadcrumb :items="[
    ['label' => 'Usuarios', 'url' => route('admin.users.index')],
    ['label' => 'Nuevo Usuario'],
]" />

<div class="card shadow-sm border-0 max-w-3xl mx-auto">
    <div class="card-body p-4">
        <h1 class="h4 mb-4 fw-bold">Crear Nuevo Usuario</h1>

        <form method="POST" action="{{ route('admin.users.store') }}" novalidate>
            @csrf

            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <x-input name="name" label="Nombre completo" :required="true" placeholder="Juan Pérez" value="{{ old('name') }}" />
                </div>
                <div class="col-12 col-md-6">
                    <x-input name="email" type="email" label="Correo electrónico" :required="true" placeholder="juan@empresa.com" value="{{ old('email') }}" />
                </div>
                <div class="col-12 col-md-6">
                    <x-input name="phone" label="Teléfono" placeholder="+52 55 0000 0000" value="{{ old('phone') }}" />
                </div>
                <div class="col-12 col-md-6">
                    <div class="mb-3">
                        <label for="role" class="form-label fw-semibold">Rol <span class="text-danger">*</span></label>
                        <select name="role" id="user-role-select" class="form-select @error('role') is-invalid @enderror" required>
                            <option value="client" {{ old('role', 'client') === 'client' ? 'selected' : '' }}>Cliente (Acceso a empresas específicas)</option>
                            <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Administrador (Acceso total)</option>
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <x-input name="password" type="password" label="Contraseña" :required="true" placeholder="Mínimo 8 caracteres" />
                </div>
                <div class="col-12 col-md-6">
                    <x-input name="password_confirmation" type="password" label="Confirmar contraseña" :required="true" placeholder="Repite la contraseña" />
                </div>

                {{-- ── Company Selection Section (Hidden for Admin, shown for Client) ── --}}
                <div class="col-12" id="company-selector-section">
                    <hr class="my-3">
                    <h5 class="fw-bold mb-2 small text-uppercase text-secondary">Asignación de Empresas</h5>
                    <p class="text-muted small mb-3">Elige las empresas a las que este usuario cliente tendrá acceso para ver sus proyectos.</p>
                    
                    <div class="mb-3">
                        <input type="text" id="company-search-input" class="form-control mb-2" placeholder="🔍 Filtrar empresas por nombre..." aria-label="Buscar empresas">
                        
                        <div class="card border border-light-subtle rounded shadow-sm p-3">
                            @if ($companies->isEmpty())
                                <x-alert type="warning" class="mb-0">
                                    No hay empresas registradas aún. Por favor crea una empresa primero.
                                </x-alert>
                            @else
                                <div class="overflow-y-auto" style="max-height: 200px;" id="companies-checkbox-list">
                                    @foreach ($companies as $company)
                                        <div class="form-check py-1 company-checkbox-item" data-company-name="{{ strtolower($company->name) }}">
                                            <input class="form-check-input" type="checkbox" name="company_ids[]" value="{{ $company->id }}" id="company-{{ $company->id }}" {{ is_array(old('company_ids')) && in_array($company->id, old('company_ids')) ? 'checked' : '' }}>
                                            <label class="form-check-label fw-medium text-dark cursor-pointer" for="company-{{ $company->id }}">
                                                {{ $company->name }} 
                                                @if($company->rfc)
                                                    <span class="text-muted small">({{ $company->rfc }})</span>
                                                @endif
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        @error('company_ids')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                    <i class="bi bi-save"></i> Guardar Usuario
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Trigger for JS initialization --}}
<div id="user-company-selector-init"></div>

@endsection
