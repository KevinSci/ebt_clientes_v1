@extends('layouts.client')

@section('title', $project->name)
@section('meta_description', 'Feed de publicaciones y evidencias del proyecto ' . $project->name)

@section('client-content')

{{-- Breadcrumb --}}
<x-breadcrumb :items="[
    ['label' => 'Mis Proyectos', 'url' => route('client.companies.projects.index', $company)],
    ['label' => $project->name],
]" class="mt-3" />

{{-- ── Project summary card ─────────────────────────────────────────────── --}}
{{-- <div class="card mb-4">
    <div class="card-body">
        <div class="d-flex flex-wrap align-items-start justify-content-between gap-2 mb-2">
            <div>
                <h1 class="h5 fw-bold mb-1">{{ $project->name }}</h1>
                <x-badge :status="$project->status" />
            </div>
            <a href="{{ route('client.companies.projects.index', $company) }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Volver
            </a>
        </div>
        <x-progress-bar :percentage="$project->progress_percentage" :status="$project->status" class="mt-3" />
    </div>
</div> --}}

{{-- ── Filters (GET-based) ──────────────────────────────────────────────── --}}
@php
    $activeFilterCount = 0;
    if ($dateFrom) $activeFilterCount++;
    if ($dateTo) $activeFilterCount++;
@endphp

<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('client.companies.projects.show', [$company, $project]) }}"
              id="feed-filter-form">

            <div class="row g-2 align-items-center">
                {{-- Search bar --}}
                <div class="col">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0 text-muted">
                            <i class="bi bi-search" aria-hidden="true"></i>
                        </span>
                        <input type="search" id="filter-search" name="search"
                               class="form-control border-start-0 bg-light" value="{{ $search }}"
                               placeholder="Buscar por título de publicación...">
                    </div>
                </div>

                {{-- Desktop Dropdown (md and up) --}}
                <div class="col-auto d-none d-md-block">
                    <div class="dropdown position-relative">
                        <button type="button" 
                                id="filterDropdownButton"
                                class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1 position-relative"
                                data-bs-toggle="dropdown" 
                                data-bs-auto-close="outside" 
                                aria-expanded="false">
                            <i class="bi bi-funnel"></i>
                            <span>Filtros</span>
                            @if ($activeFilterCount > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary">
                                    {{ $activeFilterCount }}
                                    <span class="visually-hidden">filtros activos</span>
                                </span>
                            @endif
                        </button>

                        <div class="dropdown-menu dropdown-menu-end p-3 shadow border border-light-subtle" 
                             style="min-width: 290px; z-index: 1050;">
                            
                            {{-- Header --}}
                            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                                <span class="fw-semibold text-dark small">
                                    <i class="bi bi-sliders me-1 text-primary"></i>Filtros
                                </span>
                                <button type="button" 
                                        id="closeFilterBtn"
                                        class="btn-close" 
                                        aria-label="Cerrar">
                                </button>
                            </div>

                            {{-- Filters fields --}}
                            <div class="mb-2">
                                <label for="filter-date-from-desktop" class="form-label small fw-medium mb-1">Desde</label>
                                <input type="date" id="filter-date-from-desktop" name="date_from"
                                       class="form-control form-control-sm desktop-filter-input" value="{{ $dateFrom }}">
                            </div>

                            <div class="mb-2">
                                <label for="filter-date-to-desktop" class="form-label small fw-medium mb-1">Hasta</label>
                                <input type="date" id="filter-date-to-desktop" name="date_to"
                                       class="form-control form-control-sm desktop-filter-input" value="{{ $dateTo }}">
                            </div>

                            <div class="mb-3">
                                <label for="filter-author-desktop" class="form-label small fw-medium mb-1">Publicado por</label>
                                <select id="filter-author-desktop" name="author_id" class="form-select form-select-sm text-dark desktop-filter-input">
                                    <option value="">Todos los autores</option>
                                    <option value="admin" {{ $authorId === 'admin' ? 'selected' : '' }}>EBT Consultores (Administrador)</option>
                                    @foreach($authors as $author)
                                        <option value="{{ $author->id }}" {{ $authorId == $author->id ? 'selected' : '' }}>{{ $author->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Footer actions --}}
                            <div class="d-flex gap-2 pt-2 border-top">
                                @if ($dateFrom || $dateTo || $authorId)
                                    <a href="{{ route('client.companies.projects.show', [$company, $project]) }}{{ $search ? '?search=' . urlencode($search) : '' }}"
                                       class="btn btn-outline-danger btn-sm flex-grow-1 d-flex align-items-center justify-content-center gap-1">
                                        <i class="bi bi-trash"></i>Limpiar
                                    </a>
                                @endif
                                <button type="submit" class="btn btn-primary btn-sm flex-grow-1 d-flex align-items-center justify-content-center gap-1">
                                    <i class="bi bi-check2"></i>Aplicar
                                </button>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- Mobile Button (less than md) --}}
                <div class="col-auto d-md-none">
                    <button type="button" 
                            id="filterOffcanvasButton"
                            class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1 position-relative"
                            data-bs-toggle="offcanvas" 
                            data-bs-target="#mobileFilterOffcanvas"
                            aria-controls="mobileFilterOffcanvas">
                        <i class="bi bi-funnel"></i>
                        @if ($activeFilterCount > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary">
                                {{ $activeFilterCount }}
                                <span class="visually-hidden">filtros activos</span>
                            </span>
                        @endif
                    </button>
                </div>

            </div>

        </form>
    </div>
</div>

{{-- ── Active filter tags ───────────────────────────────────────────────── --}}
@if ($search || $dateFrom || $dateTo || $authorId)
    <div class="d-flex flex-wrap gap-2 mb-3" aria-label="Filtros activos">
        @if ($search)
            <span class="badge bg-primary rounded-pill">
                <i class="bi bi-search me-1"></i>{{ $search }}
            </span>
        @endif
        @if ($dateFrom)
            <span class="badge bg-primary rounded-pill">
                <i class="bi bi-calendar-event me-1"></i>Desde {{ \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') }}
            </span>
        @endif
        @if ($dateTo)
            <span class="badge bg-primary rounded-pill">
                <i class="bi bi-calendar-event me-1"></i>Hasta {{ \Carbon\Carbon::parse($dateTo)->format('d/m/Y') }}
            </span>
        @endif
        @if ($authorId)
            <span class="badge bg-primary rounded-pill">
                <i class="bi bi-person me-1"></i>
                @if($authorId === 'admin')
                    De: EBT Consultores
                @else
                    De: {{ $authors->firstWhere('id', $authorId)?->name ?? 'Usuario' }}
                @endif
            </span>
        @endif
    </div>
@endif

@if(auth()->user()->canPublish())
    <div class="row g-4">
        {{-- Left: Post form --}}
        <div class="col-12 col-xl-5">
            <x-post-form :action="route('client.companies.projects.posts.store', [$company, $project])" />
        </div>

        {{-- Right: Posts list with scrollable container --}}
        <div class="col-12 col-xl-7">
            <h2 class="h5 mb-3">
                Publicaciones
                <span class="badge bg-secondary ms-1">{{ $posts->count() }}</span>
            </h2>

            @if ($posts->isEmpty())
                <x-alert type="info">
                    @if ($search || $dateFrom || $dateTo || $authorId)
                        No se encontraron publicaciones con los filtros aplicados.
                    @else
                        Aún no hay publicaciones en este proyecto.
                    @endif
                </x-alert>
            @else
                <x-scrollable maxHeight="650px">
                    <div class="d-flex flex-column gap-3">
                        @foreach ($posts as $post)
                            @include('client.projects._post_card', ['post' => $post])
                        @endforeach
                    </div>
                </x-scrollable>
            @endif
        </div>
    </div>
@else
    @if ($posts->isEmpty())
        <x-alert type="info">
            @if ($search || $dateFrom || $dateTo || $authorId)
                No se encontraron publicaciones con los filtros aplicados.
            @else
                Aún no hay publicaciones en este proyecto.
            @endif
        </x-alert>
    @else
        <div class="d-flex flex-column gap-3" id="posts-feed" aria-label="Feed de publicaciones">
            @foreach ($posts as $post)
                @include('client.projects._post_card', ['post' => $post])
            @endforeach
        </div>

        {{-- Pagination --}}
        <x-pagination :items="$posts" />
    @endif
@endif

<x-image-viewer-modal title="Imagen" />
<x-folder-viewer-modal title="Contenido de Carpeta" />

{{-- Mobile Filters Offcanvas (bottom drawer) --}}
<div class="offcanvas offcanvas-bottom d-md-none border-top rounded-top-4" 
     tabindex="-1" 
     id="mobileFilterOffcanvas" 
     aria-labelledby="mobileFilterOffcanvasLabel"
     style="height: auto; max-height: 80vh;">
    
    <div class="offcanvas-header border-bottom py-3">
        <h5 class="offcanvas-title h6 fw-bold text-dark d-flex align-items-center gap-1" id="mobileFilterOffcanvasLabel">
            <i class="bi bi-sliders text-primary"></i> Filtros de búsqueda
        </h5>
        <button type="button" class="btn-close shadow-none" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
    </div>

    <div class="offcanvas-body p-3">
        <div class="mb-3">
            <label for="filter-date-from-mobile" class="form-label small fw-medium mb-1">Desde</label>
            <input type="date" id="filter-date-from-mobile" name="date_from" form="feed-filter-form"
                   class="form-control mobile-filter-input" value="{{ $dateFrom }}">
        </div>

        <div class="mb-3">
            <label for="filter-date-to-mobile" class="form-label small fw-medium mb-1">Hasta</label>
            <input type="date" id="filter-date-to-mobile" name="date_to" form="feed-filter-form"
                   class="form-control mobile-filter-input" value="{{ $dateTo }}">
        </div>

        <div class="mb-4">
            <label for="filter-author-mobile" class="form-label small fw-medium mb-1">Publicado por</label>
            <select id="filter-author-mobile" name="author_id" form="feed-filter-form" class="form-select text-dark mobile-filter-input">
                <option value="">Todos los autores</option>
                <option value="admin" {{ $authorId === 'admin' ? 'selected' : '' }}>EBT Consultores (Administrador)</option>
                @foreach($authors as $author)
                    <option value="{{ $author->id }}" {{ $authorId == $author->id ? 'selected' : '' }}>{{ $author->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="d-flex gap-2 pt-2 border-top">
            @if ($dateFrom || $dateTo || $authorId)
                <a href="{{ route('client.companies.projects.show', [$company, $project]) }}{{ $search ? '?search=' . urlencode($search) : '' }}"
                   class="btn btn-outline-danger flex-grow-1 d-flex align-items-center justify-content-center gap-1 py-2">
                    <i class="bi bi-trash"></i>Limpiar
                </a>
            @endif
            <button type="submit" form="feed-filter-form" class="btn btn-primary flex-grow-1 d-flex align-items-center justify-content-center gap-1 py-2">
                <i class="bi bi-check2"></i>Aplicar
            </button>
        </div>
    </div>
</div>

@if(auth()->user()->canPublish())
    <div id="project-page-init"
         data-post-ids="{{ $posts->filter(fn($p) => $p->user_id === auth()->id())->pluck('id')->toJson() }}"
         data-ajax-store-url="{{ route('client.companies.projects.posts.store-ajax', [$company, $project]) }}"
         data-ajax-update-url-template="{{ route('client.companies.projects.posts.update-ajax', [$company, $project, '__POST_ID__']) }}"
         data-upload-url-template="{{ route('client.companies.projects.posts.attachments.upload', [$company, $project, '__POST_ID__']) }}"
         data-redirect-url="{{ route('client.companies.projects.show', [$company, $project]) }}">
    </div>
@else
    <div id="client-project-init"></div>
@endif

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Handle desktop dropdown close button
        const closeBtn = document.getElementById('closeFilterBtn');
        if (closeBtn) {
            closeBtn.addEventListener('click', function () {
                const dropdownElement = document.getElementById('filterDropdownButton');
                if (dropdownElement) {
                    const dropdown = bootstrap.Dropdown.getOrCreateInstance(dropdownElement);
                    dropdown.hide();
                }
            });
        }

        // On form submit, disable inputs from the inactive layout to prevent overriding values
        const filterForm = document.getElementById('feed-filter-form');
        if (filterForm) {
            filterForm.addEventListener('submit', function () {
                if (window.innerWidth < 768) {
                    // Mobile is active, disable desktop inputs
                    document.querySelectorAll('.desktop-filter-input').forEach(el => el.disabled = true);
                } else {
                    // Desktop is active, disable mobile inputs
                    document.querySelectorAll('.mobile-filter-input').forEach(el => el.disabled = true);
                }
            });
        }
    });
</script>
@endpush
@endsection
