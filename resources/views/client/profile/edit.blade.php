@extends('layouts.client')

@section('title', 'Configuración')

@section('client-content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-8 col-xl-6">
        <div class="mb-4">
            <h1 class="h4 mb-0"><i class="bi bi-gear-fill me-2 text-primary"></i>Configuración</h1>
            <p class="text-muted small mb-0">Actualiza los datos de tu cuenta</p>
        </div>
        
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('client.profile.update') }}" novalidate>
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

                    <x-input
                        name="phone"
                        type="text"
                        label="Teléfono"
                        :required="false"
                        :value="$user->phone"
                        placeholder="Teléfono (opcional)"
                    />

                    <h5 class="card-title mt-5 mb-4 pb-2 border-bottom">
                        <i class="bi bi-bell-fill text-primary me-2"></i>Notificaciones
                    </h5>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" role="switch" id="notifications-email-switch" name="notifications_email" value="1" {{ old('notifications_email', $user->emailNotificationsEnabled()) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold text-dark cursor-pointer" for="notifications-email-switch">
                            Recibir notificaciones por correo electrónico
                        </label>
                    </div>
                    <p class="text-muted small mb-3">Si activas esta opción, recibirás avisos por correo electrónico cuando se publiquen nuevas actualizaciones o evidencias en tus proyectos.</p>

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

        {{-- Botón de cerrar sesión al final --}}
        <div class="card shadow-sm border-0 mt-4 border-top border-danger border-3">
            <div class="card-body p-4">
                <h5 class="card-title text-danger mb-3">
                    <i class="bi bi-box-arrow-right me-2"></i>Sesión
                </h5>
                <p class="text-muted small mb-3">¿Deseas cerrar tu sesión actual en este dispositivo?</p>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger w-100 d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-box-arrow-right"></i>
                        Cerrar sesión
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
