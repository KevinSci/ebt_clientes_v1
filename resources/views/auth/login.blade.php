@extends('layouts.app')

@section('title', 'Iniciar Sesión')
@section('meta_description', 'Accede al Portal de Clientes EBT para ver tus proyectos y evidencias.')

@section('content')
<div class="bg-light min-vh-100 d-flex align-items-center">
    <div class="container">
        <div class="row justify-content-center py-5">
            <div class="col-12 col-sm-9 col-md-7 col-lg-5 col-xl-4">

                {{-- Card --}}
                <div class="card shadow-sm">
                    <div class="card-body p-4">

                        {{-- Logo / Brand --}}
                        <div class="text-center mb-4">
                            <div class="mb-3">
                                <img src="{{ asset('img/logo.svg') }}" alt="EBT Logo" class="ebt-logo-login">
                            </div>
                            <h1 class="h4">Portal de Clientes</h1>
                            <p class="text-muted small mb-0">EBT Servicios Profesionales</p>
                        </div>

                        {{-- Validation errors --}}
                        @if ($errors->any())
                            <x-alert type="danger" class="mb-3">
                                {{ $errors->first() }}
                            </x-alert>
                        @endif

                        {{-- Login form --}}
                        <form method="POST" action="{{ route('login.attempt') }}" id="login-form" novalidate>
                            @csrf

                            <x-input
                                name="email"
                                type="email"
                                label="Correo electrónico"
                                placeholder="usuario@empresa.com"
                                :required="true"
                                autocomplete="email"
                            />

                            <x-input
                                name="password"
                                type="password"
                                label="Contraseña"
                                placeholder="••••••••"
                                :required="true"
                                autocomplete="current-password"
                            />

                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox"
                                       name="remember" id="remember" value="1">
                                <label class="form-check-label small" for="remember">
                                    Recordar sesión
                                </label>
                            </div>

                            <x-button
                                type="submit"
                                variant="primary"
                                class="w-100 py-2 fw-semibold"
                                id="btn-login"
                            >
                                <i class="bi bi-box-arrow-in-right me-2"></i>
                                Iniciar Sesión
                            </x-button>

                        </form>

                        <p class="text-center text-muted mt-4 small mb-0">
                            ¿Problemas para acceder? Contacta a tu ejecutivo EBT.
                        </p>

                    </div>
                </div>
                {{-- /card --}}

            </div>
        </div>
    </div>
</div>
@endsection
