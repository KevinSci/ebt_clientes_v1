@extends('layouts.app')

@section('title', 'Iniciar Sesión')
@section('meta_description', 'Accede al Portal de Clientes EBT para ver tus proyectos y evidencias.')

@section('content')
<div class="position-relative overflow-hidden bg-primary bg-gradient d-flex flex-column justify-content-center flex-grow-1" style="min-height: calc(100vh - 138px);">
    
    {{-- Background geometric decoration --}}
    <div class="position-absolute w-100 h-100" style="inset: 0; pointer-events: none;" aria-hidden="true">
        <div class="ebt-login-page__shape ebt-login-page__shape--1"></div>
        <div class="ebt-login-page__shape ebt-login-page__shape--2"></div>
        <div class="ebt-login-page__shape ebt-login-page__shape--3"></div>
    </div>

    <div class="container position-relative z-1 py-5">
        <div class="row align-items-center justify-content-center">
            <div class="col-12 col-sm-9 col-md-7 col-lg-5 col-xl-4">
                
                {{-- Card --}}
                <div class="card border-0 shadow-lg rounded-4 p-4 p-sm-5 bg-white bg-opacity-75" style="backdrop-filter: blur(10px);">
                    
                    {{-- Logo / Brand --}}
                    <div class="text-center mb-4">
                        <div class="mb-3" aria-hidden="true">
                            <img src="{{ Vite::asset('resources/img/logo.svg') }}" alt="EBT Logo" class="img-fluid" style="height: 68px;">
                        </div>
                        <h1 class="h4 fw-bold text-dark">Portal de Clientes</h1>
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
                        <div class="mb-3 text-start text-dark">
                            <x-input
                                name="email"
                                type="email"
                                label="Correo electrónico"
                                placeholder="usuario@empresa.com"
                                :required="true"
                                autocomplete="email"
                            />
                        </div>

                        <div class="mb-3 text-start text-dark">
                            <x-input
                                name="password"
                                type="password"
                                label="Contraseña"
                                placeholder="••••••••"
                                :required="true"
                                autocomplete="current-password"
                            />
                        </div>

                        <div class="form-check mb-4 text-start text-dark">
                            <input class="form-check-input" type="checkbox"
                                   name="remember" id="remember" value="1">
                            <label class="form-check-label small" for="remember">
                                Recordar sesión
                            </label>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold shadow-sm" id="btn-login">
                            <i class="bi bi-box-arrow-in-right me-2"></i>
                            Iniciar Sesión
                        </button>
                    </form>

                    <p class="text-center text-muted mt-4 small mb-0">
                        ¿Problemas para acceder? Contacta a tu ejecutivo EBT.
                    </p>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
