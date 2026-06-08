@extends('layouts.client')

@section('title', $project->name)
@section('meta_description', 'Feed de publicaciones y evidencias del proyecto ' . $project->name)

@section('client-content')

{{-- Breadcrumb --}}
<nav aria-label="breadcrumb" class="mt-3 mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('client.projects.index') }}">Mis Proyectos</a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">{{ $project->name }}</li>
    </ol>
</nav>

{{-- ── Project summary card ─────────────────────────────────────────────── --}}
<div class="card mb-4">
    <div class="card-body">
        <div class="d-flex flex-wrap align-items-start justify-content-between gap-2 mb-2">
            <div>
                <h1 class="h5 fw-bold mb-1">{{ $project->name }}</h1>
                <x-badge :status="$project->status" />
            </div>
            <a href="{{ route('client.projects.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Volver
            </a>
        </div>
        <x-progress-bar :percentage="$project->progress_percentage" class="mt-3" />
    </div>
</div>

{{-- ── Filters (GET-based) ──────────────────────────────────────────────── --}}
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('client.projects.show', $project) }}"
              id="feed-filter-form" class="row g-2 align-items-end">

            <div class="col-12 col-sm-6 col-md-4">
                <label for="filter-search" class="form-label small fw-medium mb-1">Buscar título</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="bi bi-search" aria-hidden="true"></i></span>
                    <input type="search" id="filter-search" name="search"
                           class="form-control" value="{{ $search }}"
                           placeholder="Título del post…">
                </div>
            </div>

            <div class="col-6 col-md-3">
                <label for="filter-date-from" class="form-label small fw-medium mb-1">Desde</label>
                <input type="date" id="filter-date-from" name="date_from"
                       class="form-control form-control-sm" value="{{ $dateFrom }}">
            </div>

            <div class="col-6 col-md-3">
                <label for="filter-date-to" class="form-label small fw-medium mb-1">Hasta</label>
                <input type="date" id="filter-date-to" name="date_to"
                       class="form-control form-control-sm" value="{{ $dateTo }}">
            </div>

            <div class="col-12 col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                    <i class="bi bi-funnel me-1"></i>Filtrar
                </button>
                @if ($search || $dateFrom || $dateTo)
                    <a href="{{ route('client.projects.show', $project) }}"
                       class="btn btn-outline-secondary btn-sm" aria-label="Limpiar filtros">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @endif
            </div>

        </form>
    </div>
</div>

{{-- ── Active filter tags ───────────────────────────────────────────────── --}}
@if ($search || $dateFrom || $dateTo)
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
    </div>
@endif

{{-- ── Posts feed ───────────────────────────────────────────────────────── --}}
@if ($posts->isEmpty())
    <x-alert type="info">
        @if ($search || $dateFrom || $dateTo)
            No se encontraron publicaciones con los filtros aplicados.
        @else
            Aún no hay publicaciones en este proyecto.
        @endif
    </x-alert>
@else
    <div class="d-flex flex-column gap-3" id="posts-feed" aria-label="Feed de publicaciones">

        @foreach ($posts as $post)
            <article class="card" data-post-id="{{ $post->id }}">
                <div class="card-body">

                    {{-- Post header --}}
                    <div class="d-flex align-items-start gap-3 mb-3">
                        <span class="badge rounded-circle bg-primary d-inline-flex align-items-center justify-content-center flex-shrink-0"
                              style="width:40px;height:40px" aria-hidden="true">
                            <i class="bi bi-megaphone-fill"></i>
                        </span>
                        <div>
                            <h2 class="h6 mb-0 fw-bold">{{ $post->title }}</h2>
                            @if ($post->published_at)
                                <time datetime="{{ $post->published_at->toIso8601String() }}"
                                      class="small text-muted">
                                    <i class="bi bi-clock me-1" aria-hidden="true"></i>
                                    {{ $post->published_at->translatedFormat('d \d\e F \d\e Y — H:i') }}
                                </time>
                            @endif
                        </div>
                    </div>

                    {{-- Post description with read more/less (Vanilla JS) --}}
                    <div class="mb-0">
                        <x-read-more :text="$post->description" />
                    </div>

                    {{-- Attachments (images + documents) --}}
                    @if ($post->attachments->count() > 0)
                        <div class="mt-3">
                            <x-attachment-grid
                                :attachments="$post->attachments"
                                :postId="$post->id"
                            />
                        </div>
                    @endif

                </div>
            </article>
        @endforeach

    </div>

    {{-- Pagination --}}
    <x-pagination :items="$posts" />
@endif

<x-image-viewer-modal title="Imagen" />

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (typeof window.initReadMore === 'function') {
            window.initReadMore();
        }
        if (typeof window.initImageViewer === 'function') {
            window.initImageViewer('modal-image-viewer', 'viewer-img', 'viewer-filename', 'btn-viewer-download');
        }
    });
</script>
@endpush
