@extends('layouts.admin')

@section('title', 'Configuración')

@section('admin-content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-8 col-xl-6">
        <div class="mb-4">
            <h1 class="h4 mb-0"><i class="bi bi-gear-fill me-2 text-primary"></i>Configuración</h1>
            <p class="text-muted small mb-0">Actualiza los datos de tu cuenta de administrador</p>
        </div>
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.profile.update') }}" novalidate>
                    @csrf
                    @method('PUT')

                    <h5 class="card-title mb-4 pb-2 border-bottom">
                        <i class="bi bi-person-fill-gear text-primary me-2"></i>Datos Personales
                    </h5>

                    <x-input
                        name="name"
                        label="Nombre completo"
                        :required="true"
                        :value="$user->name"
                        placeholder="Nombre completo"
                    />

                    <x-input
                        name="email"
                        type="email"
                        label="Correo electrónico"
                        :required="true"
                        :value="$user->email"
                        placeholder="correo@ejemplo.com"
                    />

                    <h5 class="card-title mt-5 mb-4 pb-2 border-bottom">
                        <i class="bi bi-shield-lock-fill text-danger me-2"></i>Cambiar Contraseña
                    </h5>
                    <p class="text-muted small mb-3">Deja estos campos en blanco si no deseas cambiar tu contraseña.</p>

                    <x-input
                        name="password"
                        type="password"
                        label="Nueva contraseña"
                        placeholder="Mínimo 8 caracteres"
                        helpText="La contraseña debe tener al menos 8 caracteres."
                    />

                    <x-input
                        name="password_confirmation"
                        type="password"
                        label="Confirmar nueva contraseña"
                        placeholder="Repite la contraseña"
                    />

                    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                        <x-button type="submit" variant="primary" icon="bi-save">
                            Guardar Cambios
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
