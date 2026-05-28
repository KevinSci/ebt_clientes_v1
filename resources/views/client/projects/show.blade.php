@extends('layouts.client')

@section('title', $project->name)
@section('meta_description', 'Feed de publicaciones y evidencias del proyecto ' . $project->name)

@section('client-content')

{{-- Breadcrumb --}}
<nav aria-label="breadcrumb" class="mt-3 mb-4">
    <ol class="breadcrumb bg-transparent p-0 m-0 small">
        <li class="breadcrumb-item">
            <a href="{{ route('client.projects.index') }}" class="text-decoration-none">Mis Proyectos</a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">{{ $project->name }}</li>
    </ol>
</nav>

{{-- ── Project summary card ─────────────────────────────────────────────── --}}
<x-card class="mb-4 border-0 shadow-sm rounded-4 bg-white">
    <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-3">
        <div>
            <h1 class="h4 fw-bold mb-2 text-dark">{{ $project->name }}</h1>
            <x-badge :status="$project->status" />
        </div>
        <a href="{{ route('client.projects.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-medium">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
    <div class="mt-4 pt-2 border-top">
        <x-progress-bar :percentage="$project->progress_percentage" />
    </div>
</x-card>

{{-- ── Filters (GET-based) ──────────────────────────────────────────────── --}}
<div class="card bg-light border-0 shadow-sm mb-4 rounded-4">
    <div class="card-body">
        <form method="GET" action="{{ route('client.projects.show', $project) }}"
              id="feed-filter-form" class="row g-3 align-items-end">

            <div class="col-12 col-md-5 col-lg-4">
                <label for="filter-search" class="form-label small fw-semibold text-dark mb-1">Buscar título</label>
                <div class="input-group input-group-sm shadow-sm">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted" aria-hidden="true"></i></span>
                    <input type="search" id="filter-search" name="search"
                           class="form-control border-start-0 ps-0" value="{{ $search }}"
                           placeholder="Título del post…">
                </div>
            </div>

            <div class="col-6 col-md-3 col-lg-2">
                <label for="filter-date-from" class="form-label small fw-semibold text-dark mb-1">Desde</label>
                <input type="date" id="filter-date-from" name="date_from"
                       class="form-control form-control-sm shadow-sm" value="{{ $dateFrom }}">
            </div>

            <div class="col-6 col-md-3 col-lg-2">
                <label for="filter-date-to" class="form-label small fw-semibold text-dark mb-1">Hasta</label>
                <input type="date" id="filter-date-to" name="date_to"
                       class="form-control form-control-sm shadow-sm" value="{{ $dateTo }}">
            </div>

            <div class="col-12 col-lg-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm flex-grow-1 fw-medium shadow-sm">
                    <i class="bi bi-funnel me-1"></i>Filtrar
                </button>
                @if ($search || $dateFrom || $dateTo)
                    <a href="{{ route('client.projects.show', $project) }}"
                       class="btn btn-outline-secondary btn-sm shadow-sm" aria-label="Limpiar filtros">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @endif
            </div>

        </form>
    </div>
</div>

{{-- ── Active filter tags ───────────────────────────────────────────────── --}}
@if ($search || $dateFrom || $dateTo)
    <div class="d-flex flex-wrap gap-2 mb-4" aria-label="Filtros activos">
        @if ($search)
            <span class="badge bg-primary text-white rounded-pill px-3 py-2 shadow-sm">
                <i class="bi bi-search me-1"></i>{{ $search }}
            </span>
        @endif
        @if ($dateFrom)
            <span class="badge bg-primary text-white rounded-pill px-3 py-2 shadow-sm">
                <i class="bi bi-calendar-event me-1"></i>Desde {{ \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') }}
            </span>
        @endif
        @if ($dateTo)
            <span class="badge bg-primary text-white rounded-pill px-3 py-2 shadow-sm">
                <i class="bi bi-calendar-event me-1"></i>Hasta {{ \Carbon\Carbon::parse($dateTo)->format('d/m/Y') }}
            </span>
        @endif
    </div>
@endif

{{-- ── Posts feed ───────────────────────────────────────────────────────── --}}
@if ($posts->isEmpty())
    <x-alert type="info" class="shadow-sm border-0 bg-white">
        @if ($search || $dateFrom || $dateTo)
            No se encontraron publicaciones con los filtros aplicados.
        @else
            Aún no hay publicaciones en este proyecto.
        @endif
    </x-alert>
@else
    <div class="ebt-feed" id="posts-feed" aria-label="Feed de publicaciones">

        @foreach ($posts as $post)
            <article class="ebt-feed__post shadow-sm border-0 bg-white rounded-4 p-4 mb-4" data-post-id="{{ $post->id }}">

                {{-- Post header --}}
                <div class="d-flex align-items-start gap-3 mb-3 pb-3 border-bottom">
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px;" aria-hidden="true">
                        <i class="bi bi-megaphone-fill fs-5"></i>
                    </div>
                    <div>
                        <h2 class="h5 mb-1 fw-bold text-dark">{{ $post->title }}</h2>
                        @if ($post->published_at)
                            <time datetime="{{ $post->published_at->toIso8601String() }}"
                                  class="small text-muted fw-medium">
                                <i class="bi bi-clock me-1 text-primary" aria-hidden="true"></i>
                                {{ $post->published_at->translatedFormat('d \d\e F \d\e Y — H:i') }}
                            </time>
                        @endif
                    </div>
                </div>

                {{-- Post description with read more/less (Vanilla JS) --}}
                <div class="text-dark">
                    @php
                        $desc      = $post->description;
                        $needsTrim = mb_strlen($desc) > 150;
                        $preview   = $needsTrim ? mb_substr($desc, 0, 150) : $desc;
                    @endphp

                    <div class="ebt-read-more" data-needs-trim="{{ $needsTrim ? 'true' : 'false' }}">
                        <p class="ebt-read-more__text mb-0" style="white-space: pre-line; line-height: 1.6;">
                            <span class="ebt-read-more__preview">{{ $preview }}</span>
                            @if ($needsTrim)
                                <span class="ebt-read-more__ellipsis">…</span>
                                <span class="ebt-read-more__full d-none">{{ mb_substr($desc, 150) }}</span>
                            @endif
                        </p>
                        @if ($needsTrim)
                            <button type="button"
                                    class="btn btn-link text-primary text-decoration-none fw-semibold p-0 mt-2 small ebt-read-more__btn"
                                    aria-expanded="false">
                                Ver más <i class="bi bi-chevron-down ms-1" style="font-size: 0.8em;"></i>
                            </button>
                        @endif
                    </div>
                </div>

                {{-- Attachments (images + documents) --}}
                @if ($post->attachments->count() > 0)
                    <div class="mt-4 pt-3 border-top border-light">
                        <h4 class="h6 text-muted mb-3 fw-semibold">Archivos Adjuntos</h4>
                        <x-attachment-grid
                            :attachments="$post->attachments"
                            :postId="$post->id"
                        />
                    </div>
                @endif

            </article>
        @endforeach

    </div>

    {{-- Pagination --}}
    @if ($posts->hasPages())
        <div class="mt-5 d-flex justify-content-center">
            {{ $posts->links() }}
        </div>
    @endif
@endif

{{-- ── Image viewer modal ───────────────────────────────────────────────── --}}
<x-modal id="modal-image-viewer" title="Imagen" size="xl">
    <div class="text-center bg-dark rounded-3 d-flex align-items-center justify-content-center p-2 ebt-viewer" style="min-height: 200px;">
        <img id="viewer-img" src="" alt="" class="img-fluid rounded" style="max-height: 75vh;">
    </div>
    <x-slot:footer>
        <span class="text-muted small me-auto fw-medium" id="viewer-filename"></span>
        <a id="btn-viewer-download" href="#" download
           class="btn btn-primary shadow-sm" target="_blank">
            <i class="bi bi-download me-2"></i>Descargar
        </a>
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
    </x-slot:footer>
</x-modal>

@endsection

@push('scripts')
<script type="module">
    import { initReadMore }     from @json(Vite::asset('resources/js/modules/readMore.js'));
    import { initImageViewer }  from @json(Vite::asset('resources/js/modules/imageViewer.js'));

    initReadMore();
    initImageViewer('modal-image-viewer', 'viewer-img', 'viewer-filename', 'btn-viewer-download');
</script>
<style>
    .ebt-feed__post { transition: transform 0.2s ease, box-shadow 0.2s ease; }
    .ebt-feed__post:hover { transform: translateY(-2px); box-shadow: 0 8px 30px rgba(35, 38, 145, 0.1) !important; }
</style>
@endpush
