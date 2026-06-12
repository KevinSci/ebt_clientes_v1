@extends('layouts.admin')

@section('title', $company->name)

@section('admin-content')

{{-- Breadcrumb --}}
<x-breadcrumb :items="[
    ['label' => 'Empresas', 'url' => route('admin.companies.index')],
    ['label' => $company->name],
]" />

{{-- ── Company profile header ─────────────────────────────────────────── --}}
<div class="card mb-4" id="company-info-card">
    <div class="card-body">
        <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-3 justify-content-between">
            <div class="d-flex align-items-center gap-3 flex-grow-1 min-w-0 w-100 w-md-auto">
                <x-avatar size="lg" variant="primary" icon="bi-building" />
                <div class="min-w-0 flex-grow-1">
                    <h1 class="h4 mb-1 fw-bold text-break">{{ $company->name }}</h1>
                    @if ($company->rfc)
                        <p class="mb-1 text-muted small text-break d-flex align-items-start gap-2">
                            <i class="bi bi-card-text mt-1 flex-shrink-0" aria-hidden="true"></i>
                            <span class="text-break">RFC: {{ $company->rfc }}</span>
                        </p>
                    @endif
                    <p class="mb-1 text-muted small text-break d-flex align-items-start gap-2">
                        <i class="bi bi-patch-check mt-1 flex-shrink-0" aria-hidden="true"></i>
                        <span class="text-break">Régimen: {{ $company->tax_regime === 'moral' ? 'Persona Moral' : 'Persona Física' }}</span>
                    </p>
                    @if ($company->phone)
                        <p class="mb-1 text-muted small text-break d-flex align-items-start gap-2">
                            <i class="bi bi-telephone mt-1 flex-shrink-0" aria-hidden="true"></i>
                            <span class="text-break">{{ $company->phone }}</span>
                        </p>
                    @endif
                    @if ($company->address)
                        <p class="mb-0 text-muted small text-break d-flex align-items-start gap-2">
                            <i class="bi bi-geo-alt mt-1 flex-shrink-0" aria-hidden="true"></i>
                            <span class="text-break">{{ $company->address }}</span>
                        </p>
                    @endif
                </div>
            </div>
            <div class="d-flex align-items-center justify-content-between justify-content-md-end gap-4 w-100 w-md-auto mt-3 mt-md-0">
                <div class="text-center flex-shrink-0">
                    <p class="h3 fw-bold text-primary mb-0">{{ $company->projects->count() }}</p>
                    <p class="small text-muted mb-0">{{ Str::plural('Proyecto', $company->projects->count()) }}</p>
                </div>
                <div class="d-flex gap-2 flex-shrink-0">
                    <button class="btn btn-outline-secondary d-flex align-items-center gap-2"
                            data-bs-toggle="modal"
                            data-bs-target="#modal-edit-company"
                            id="btn-open-edit-company">
                        <i class="bi bi-pencil"></i> <span class="d-none d-sm-inline">Editar</span>
                    </button>
                    <button class="btn btn-outline-danger d-flex align-items-center gap-2"
                            data-bs-toggle="modal"
                            data-bs-target="#modal-delete-company"
                            id="btn-open-delete-company">
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

@if ($company->projects->isEmpty())
    <x-alert type="info">Esta empresa no tiene proyectos aún.</x-alert>
@else
    <x-scrollable maxHeight="500px">
        <div class="d-flex flex-column gap-3 p-1">
            @foreach ($company->projects as $project)
                <x-project-card 
                    :project="$project" 
                    :href="route('admin.companies.projects.show', [$company, $project])" 
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

    <form method="POST" action="{{ route('admin.companies.projects.store', $company) }}" id="form-create-project" novalidate>
        @csrf
        <input type="hidden" name="form_id" value="create_project">

        <div class="row g-3">
            <div class="col-12">
                <x-input name="name" label="Nombre del proyecto" :required="true" placeholder="Ej. Auditoría Anual" />
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
{{-- Modal: Edit company                                                   --}}
{{-- ══════════════════════════════════════════════════════════════════════ --}}
<x-modal id="modal-edit-company" title="Editar Empresa" size="lg">

    <form method="POST" action="{{ route('admin.companies.update', $company) }}" id="form-edit-company" novalidate>
        @csrf
        @method('PUT')

        <input type="hidden" name="form_id" value="edit_company">

        <div class="row g-3">
            <div class="col-12 col-md-6">
                <x-input name="name" label="Nombre de la empresa" :required="true" placeholder="Empresa S.A. de C.V." :value="$company->name" />
            </div>
            <div class="col-12 col-md-6">
                <x-input name="rfc" label="RFC" placeholder="RFC de 12 o 13 caracteres" :value="$company->rfc" />
            </div>
            <div class="col-12 col-md-6">
                <div class="mb-3">
                    <label for="edit_tax_regime" class="form-label fw-semibold">Régimen Fiscal <span class="text-danger">*</span></label>
                    <select name="tax_regime" id="edit_tax_regime" class="form-select @error('tax_regime') is-invalid @enderror" required>
                        <option value="moral" {{ old('tax_regime', $company->tax_regime) === 'moral' ? 'selected' : '' }}>Persona Moral</option>
                        <option value="fisica" {{ old('tax_regime', $company->tax_regime) === 'fisica' ? 'selected' : '' }}>Persona Física</option>
                    </select>
                    @error('tax_regime')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-12 col-md-6">
                <x-input name="phone" label="Teléfono" placeholder="+52 55 0000 0000" :value="$company->phone" />
            </div>
            <div class="col-12">
                <x-input name="address" label="Dirección física" placeholder="Av. Paseo de la Reforma #123, Col. Centro" :value="$company->address" />
            </div>
        </div>

    </form>

    <x-slot:footer>
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
            Cancelar
        </button>
        <x-button type="submit" form="form-edit-company" variant="primary" icon="bi-check-lg">
            Guardar Cambios
        </x-button>
    </x-slot:footer>

</x-modal>

{{-- ══════════════════════════════════════════════════════════════════════ --}}
{{-- Modal: Delete company                                                 --}}
{{-- ══════════════════════════════════════════════════════════════════════ --}}
<x-modal id="modal-delete-company" title="Eliminar Empresa" size="md">

    <form method="POST" action="{{ route('admin.companies.destroy', $company) }}" id="form-delete-company">
        @csrf
        @method('DELETE')

        <div class="text-center my-3">
            <i class="bi bi-exclamation-triangle text-danger fs-1"></i>
            <h3 class="h5 mt-3 fw-bold">¿Estás seguro de que deseas eliminar esta empresa?</h3>
            <p class="text-muted small px-3">
                Esta acción es irreversible y también eliminará de forma lógica todos los proyectos asociados a <strong>{{ $company->name }}</strong>.
            </p>
        </div>

    </form>

    <x-slot:footer>
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
            Cancelar
        </button>
        <x-button type="submit" form="form-delete-company" variant="danger" icon="bi-trash">
            Eliminar Empresa
        </x-button>
    </x-slot:footer>

</x-modal>

@if ($errors->any())
    <div data-reopen-form-id="{{ old('form_id') }}"
         data-modal-map='{"edit_company":"modal-edit-company","create_project":"modal-create-project"}'></div>
@endif

@endsection
