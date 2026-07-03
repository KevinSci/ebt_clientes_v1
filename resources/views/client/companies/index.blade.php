@extends('layouts.app')

@section('title', 'Seleccionar Empresa')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
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
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3 g-md-4 justify-content-center">
                    @foreach ($companies as $company)
                        <div class="col">
                            <x-company-card :company="$company" />
                        </div>
                    @endforeach
                </div>
            @endif

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
