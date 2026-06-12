@extends('layouts.client')

@section('title', 'Mis Proyectos')
@section('meta_description', 'Consulta el estado y avance de todos tus proyectos activos e históricos.')

@section('client-content')

{{-- Page header --}}
<div class="mb-4 mt-3">
    <h1 class="h4 mb-1">Mis Proyectos</h1>
    <p class="text-muted small mb-0">
        <i class="bi bi-building me-1"></i>{{ $company->name }}
    </p>
</div>

{{-- ── Active projects ──────────────────────────────────────────────────── --}}
<section aria-labelledby="section-active" class="mb-5">
    <h2 class="h5 border-bottom pb-2 mb-3" id="section-active">
        Proyectos Activos
        <span class="badge bg-primary ms-2">{{ $activeProjects->count() }}</span>
    </h2>

    @if ($activeProjects->isEmpty())
        <x-alert type="info">No tienes proyectos activos en este momento.</x-alert>
    @else
        <div class="row g-3">
            @foreach ($activeProjects as $project)
                <div class="col-12 col-md-6">
                    <x-project-card 
                        :project="$project" 
                        :href="route('client.companies.projects.show', [$company, $project])" 
                        linkText="Ver feed" 
                    />
                </div>
            @endforeach
        </div>
    @endif
</section>

{{-- ── Historical projects ──────────────────────────────────────────────── --}}
@if ($historicalProjects->isNotEmpty())
<section aria-labelledby="section-history">
    <h2 class="h5 border-bottom pb-2 mb-3" id="section-history">
        <i class="bi bi-archive-fill text-secondary me-2" aria-hidden="true"></i>
        Historial
        <span class="badge bg-secondary ms-2">{{ $historicalProjects->count() }}</span>
    </h2>

    <div class="row g-3">
        @foreach ($historicalProjects as $project)
            <div class="col-12 col-md-6">
                <x-project-card 
                    :project="$project" 
                    :href="route('client.companies.projects.show', [$company, $project])" 
                    :historical="true" 
                />
            </div>
        @endforeach
    </div>
</section>
@endif

@endsection
