@extends('layouts.client')

@section('title', 'Mis Proyectos')
@section('meta_description', 'Consulta el estado y avance de todos tus proyectos activos e históricos.')

@section('client-content')

{{-- Page header --}}
<div class="ebt-page-header mb-4 mt-3">
    <h1 class="ebt-page-header__title h4 mb-1">Mis Proyectos</h1>
    @if (auth()->user()->company_name)
        <p class="text-muted small mb-0">
            <i class="bi bi-building me-1"></i>{{ auth()->user()->company_name }}
        </p>
    @endif
</div>

{{-- ── Active projects ──────────────────────────────────────────────────── --}}
<section aria-labelledby="section-active" class="mb-5">
    <h2 class="h5 ebt-section-title mb-3" id="section-active">
        <i class="bi bi-lightning-charge-fill text-warning me-2" aria-hidden="true"></i>
        Proyectos Activos
        <span class="badge bg-primary ms-2">{{ $activeProjects->count() }}</span>
    </h2>

    @if ($activeProjects->isEmpty())
        <x-alert type="info">No tienes proyectos activos en este momento.</x-alert>
    @else
        <div class="row g-3">
            @foreach ($activeProjects as $project)
                <div class="col-12 col-md-6">
                    <a href="{{ route('client.projects.show', $project) }}"
                       class="text-decoration-none ebt-project-card-link">
                        <x-card class="ebt-project-card ebt-project-card--active h-100">
                            <div class="d-flex align-items-start justify-content-between gap-2 mb-1">
                                <h3 class="h6 mb-0 fw-bold ebt-project-card__name">{{ $project->name }}</h3>
                                <x-badge :status="$project->status" />
                            </div>
                            <p class="text-muted small mb-3">
                                <i class="bi bi-calendar3 me-1"></i>
                                Iniciado {{ $project->created_at->diffForHumans() }}
                            </p>
                            <x-progress-bar :percentage="$project->progress_percentage" />
                            <p class="text-end small text-primary fw-medium mt-2 mb-0">
                                Ver feed <i class="bi bi-arrow-right ms-1"></i>
                            </p>
                        </x-card>
                    </a>
                </div>
            @endforeach
        </div>
    @endif
</section>

{{-- ── Historical projects ──────────────────────────────────────────────── --}}
@if ($historicalProjects->isNotEmpty())
<section aria-labelledby="section-history">
    <h2 class="h5 ebt-section-title mb-3" id="section-history">
        <i class="bi bi-archive-fill text-secondary me-2" aria-hidden="true"></i>
        Historial
        <span class="badge bg-secondary ms-2">{{ $historicalProjects->count() }}</span>
    </h2>

    <div class="row g-3">
        @foreach ($historicalProjects as $project)
            <div class="col-12 col-md-6">
                <a href="{{ route('client.projects.show', $project) }}"
                   class="text-decoration-none ebt-project-card-link">
                    <x-card class="ebt-project-card ebt-project-card--historical h-100">
                        <div class="d-flex align-items-start justify-content-between gap-2 mb-1">
                            <h3 class="h6 mb-0 fw-semibold text-muted ebt-project-card__name">
                                {{ $project->name }}
                            </h3>
                            <x-badge :status="$project->status" />
                        </div>
                        <p class="text-muted small mb-3">
                            <i class="bi bi-calendar3 me-1"></i>
                            {{ $project->created_at->format('d/m/Y') }}
                        </p>
                        <x-progress-bar :percentage="$project->progress_percentage" />
                    </x-card>
                </a>
            </div>
        @endforeach
    </div>
</section>
@endif

@endsection
