@extends('layouts.app')

@section('title', 'Seleccionar Empresa')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">
            <div class="text-center mb-4">
                <i class="bi bi-building text-primary display-4 mb-2"></i>
                <h1 class="h3 fw-bold">Selecciona tu Empresa</h1>
                <p class="text-muted">Tienes acceso a múltiples cuentas comerciales. Por favor selecciona una para continuar.</p>
            </div>

            @if ($companies->isEmpty())
                <x-alert type="danger" class="text-center py-4">
                    <i class="bi bi-exclamation-triangle display-6 mb-2 d-block text-danger"></i>
                    <span class="fw-bold d-block mb-1">Sin empresas asociadas</span>
                    No estás asociado a ninguna empresa. Por favor contacta al administrador de EBT para obtener acceso.
                </x-alert>
            @else
                <div class="d-flex flex-column gap-3">
                    @foreach ($companies as $company)
                        <div class="card shadow-sm border-0 border-start border-4 border-primary hover-shadow transition-all duration-200">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="min-w-0 flex-grow-1">
                                        <h5 class="fw-bold mb-1 text-dark text-truncate">
                                            {{ $company->name }}
                                        </h5>
                                        @if ($company->rfc)
                                            <span class="badge text-bg-light border text-secondary small mb-2">
                                                RFC: {{ $company->rfc }}
                                            </span>
                                        @endif
                                        @if ($company->address)
                                            <p class="text-muted small mb-0 text-truncate">
                                                <i class="bi bi-geo-alt me-1"></i>{{ $company->address }}
                                            </p>
                                        @endif
                                    </div>
                                    <div class="ms-3 flex-shrink-0">
                                        <a href="{{ route('client.companies.projects.index', $company) }}" class="btn btn-primary d-flex align-items-center gap-2">
                                            Ingresar <i class="bi bi-arrow-right-short fs-5"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="text-center mt-4">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-link text-secondary text-decoration-none small">
                        <i class="bi bi-box-arrow-left me-1"></i> Cerrar sesión
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .hover-shadow:hover {
        transform: translateY(-2px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
    }
    .transition-all {
        transition: all 0.2s ease-in-out;
    }
</style>
@endsection
