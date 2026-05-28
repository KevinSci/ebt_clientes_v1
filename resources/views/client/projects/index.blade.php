@extends('layouts.client')

@section('title', 'Mis Proyectos')
@section('meta_description', 'Consulta el estado y avance de todos tus proyectos activos e históricos.')

@section('client-content')

{{-- Page header --}}
<div class="mb-4 mt-3">
    <h1 class="h3 fw-bold mb-1 text-dark">Mis Proyectos</h1>
    @if (auth()->user()->company_name)
        <p class="text-muted small mb-0 fw-medium">
            <i class="bi bi-building me-1 text-primary"></i>{{ auth()->user()->company_name }}
        </p>
    @endif
</div>

{{-- ── Active projects ──────────────────────────────────────────────────── --}}
<section aria-labelledby="section-active" class="mb-5">
    <h2 class="h5 fw-bold text-dark border-bottom pb-2 mb-4 d-flex align-items-center" id="section-active">
        <i class="bi bi-lightning-charge-fill text-warning me-2" aria-hidden="true"></i>
        Proyectos Activos
        <span class="badge rounded-pill bg-primary ms-2 fs-6">{{ $activeProjects->count() }}</span>
    </h2>

    @if ($activeProjects->isEmpty())
        <x-alert type="info" class="shadow-sm bg-white border-0">No tienes proyectos activos en este momento.</x-alert>
    @else
        <div class="row g-4">
            @foreach ($activeProjects as $project)
                <div class="col-12 col-md-6">
                    <a href="{{ route('client.projects.show', $project) }}"
                       class="text-decoration-none d-block h-100">
                        <x-card class="h-100 border-start border-4 border-primary shadow-sm hover-shadow transition-transform">
                            <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                                <h3 class="h6 mb-0 fw-bold text-dark">{{ $project->name }}</h3>
                                <x-badge :status="$project->status" />
                            </div>
                            <p class="text-muted small mb-4">
                                <i class="bi bi-calendar3 me-1"></i>
                                Iniciado {{ $project->created_at->diffForHumans() }}
                            </p>
                            <x-progress-bar :percentage="$project->progress_percentage" />
                            <div class="d-flex justify-content-end mt-4 pt-3 border-top border-light">
                                <span class="small text-primary fw-semibold">
                                    Ver publicaciones <i class="bi bi-arrow-right ms-1"></i>
                                </span>
                            </div>
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
    <h2 class="h5 fw-bold text-dark border-bottom pb-2 mb-4 d-flex align-items-center" id="section-history">
        <i class="bi bi-archive-fill text-secondary me-2" aria-hidden="true"></i>
        Historial
        <span class="badge rounded-pill bg-secondary ms-2 fs-6">{{ $historicalProjects->count() }}</span>
    </h2>

    <div class="row g-4">
        @foreach ($historicalProjects as $project)
            <div class="col-12 col-md-6">
                <a href="{{ route('client.projects.show', $project) }}"
                   class="text-decoration-none d-block h-100 opacity-75 hover-opacity-100 transition-opacity">
                    <x-card class="h-100 border-start border-4 border-secondary shadow-sm hover-shadow transition-transform bg-light">
                        <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                            <h3 class="h6 mb-0 fw-bold text-muted">
                                {{ $project->name }}
                            </h3>
                            <x-badge :status="$project->status" />
                        </div>
                        <p class="text-muted small mb-4">
                            <i class="bi bi-calendar3 me-1"></i>
                            Finalizado el {{ $project->updated_at->format('d/m/Y') }}
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

@push('scripts')
<style>
    .hover-shadow { transition: box-shadow 0.2s ease, transform 0.2s ease; }
    .hover-shadow:hover { box-shadow: 0 4px 20px rgba(35, 38, 145, 0.1) !important; transform: translateY(-2px); }
    .transition-opacity { transition: opacity 0.2s ease; }
    .hover-opacity-100:hover { opacity: 1 !important; }
</style>
@endpush
