@extends('layouts.admin')

@section('title', 'Usuarios')

@section('admin-content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h1 class="h4 mb-0">Usuarios</h1>
        <p class="text-muted small mb-0">Gestión de usuarios y accesos a empresas</p>
    </div>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary d-flex align-items-center gap-2" id="btn-create-user">
        <i class="bi bi-person-plus-fill"></i> Nuevo Usuario
    </a>
</div>

{{-- ── Search bar ───────────────────────────────────────────────────────── --}}
<form method="GET" action="{{ route('admin.users.index') }}" class="mb-4" role="search">
    <div class="input-group">
        <span class="input-group-text">
            <i class="bi bi-search" aria-hidden="true"></i>
        </span>
        <input
            type="search"
            name="search"
            id="search-users"
            class="form-control"
            placeholder="Buscar por nombre, email o teléfono…"
            value="{{ $search }}"
            aria-label="Buscar usuarios"
        >
        <button type="submit" class="btn btn-primary">Buscar</button>
        @if ($search)
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary" aria-label="Limpiar búsqueda">
                <i class="bi bi-x-lg"></i>
            </a>
        @endif
    </div>
</form>

{{-- ── Users List ──────────────────────────────────────────────────────── --}}
@if ($users->isEmpty())
    <x-alert type="info">
        No se encontraron usuarios{{ $search ? " para «{$search}»" : '' }}.
    </x-alert>
@else
    <div class="card shadow-sm border-0 mb-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="users-table">
                <thead class="table-light">
                    <tr>
                        <th scope="col">Usuario</th>
                        <th scope="col">Rol</th>
                        <th scope="col">Teléfono</th>
                        <th scope="col">Empresas Asociadas</th>
                        <th scope="col" class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <x-avatar size="sm" variant="{{ $user->isAdmin() ? 'danger' : 'primary' }}" icon="bi-person-fill" />
                                    <div>
                                        <h6 class="mb-0 fw-bold text-dark">{{ $user->name }}</h6>
                                        <span class="text-muted small">{{ $user->email }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if ($user->isAdmin())
                                    <span class="badge text-bg-danger border">Administrador</span>
                                @else
                                    <span class="badge text-bg-primary border">Cliente</span>
                                @endif
                            </td>
                            <td>
                                <span class="text-muted small">{{ $user->phone ?? '—' }}</span>
                            </td>
                            <td>
                                @if ($user->isAdmin())
                                    <span class="text-muted small italic">N/A (Acceso Total)</span>
                                @elseif ($user->companies->isEmpty())
                                    <span class="text-danger small fw-semibold">
                                        <i class="bi bi-exclamation-triangle-fill me-1"></i>Sin empresas asociadas
                                    </span>
                                @else
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach ($user->companies as $company)
                                            <span class="badge text-bg-light border text-secondary small">
                                                <i class="bi bi-building me-1"></i>{{ $company->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-flex gap-2 justify-content-end">
                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1">
                                        <i class="bi bi-pencil"></i> Editar
                                    </a>
                                    
                                    @if (auth()->id() !== $user->id)
                                        <button class="btn btn-sm btn-outline-danger d-flex align-items-center gap-1"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modal-delete-user-{{ $user->id }}">
                                            <i class="bi bi-trash"></i> Eliminar
                                        </button>
                                    @else
                                        <button class="btn btn-sm btn-outline-danger d-flex align-items-center gap-1" disabled title="No puedes eliminarte a ti mismo">
                                            <i class="bi bi-trash"></i> Eliminar
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        {{-- Delete User Modal (Scoped to user loop) --}}
                        @if (auth()->id() !== $user->id)
                            <x-modal id="modal-delete-user-{{ $user->id }}" title="Eliminar Usuario" size="md">
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" id="form-delete-user-{{ $user->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <div class="text-center my-3">
                                        <i class="bi bi-exclamation-triangle text-danger fs-1"></i>
                                        <h3 class="h5 mt-3 fw-bold">¿Estás seguro de que deseas eliminar este usuario?</h3>
                                        <p class="text-muted small px-3">
                                            Esta acción es irreversible y desvinculará a <strong>{{ $user->name }}</strong> de todas las empresas. Su cuenta de acceso será eliminada.
                                        </p>
                                    </div>
                                </form>
                                <x-slot:footer>
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <x-button type="submit" form="form-delete-user-{{ $user->id }}" variant="danger" icon="bi-trash">
                                        Eliminar Usuario
                                    </x-button>
                                </x-slot:footer>
                            </x-modal>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    <x-pagination :items="$users" />
@endif

@endsection
