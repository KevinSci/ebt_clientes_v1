@extends('layouts.admin')

@section('title', $project->name)

@section('admin-content')

{{-- Breadcrumb --}}
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb bg-transparent p-0 m-0 small">
        <li class="breadcrumb-item">
            <a href="{{ route('admin.clients.index') }}" class="text-decoration-none">Clientes</a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{ route('admin.clients.show', $client) }}" class="text-decoration-none">{{ $client->name }}</a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">{{ $project->name }}</li>
    </ol>
</nav>

{{-- ── Project header ───────────────────────────────────────────────────── --}}
<x-card class="mb-4 shadow-sm border-0 bg-white">
    <div class="d-flex flex-wrap align-items-start justify-content-between gap-4 p-2">
        <div class="flex-grow-1">
            <h1 class="h3 mb-2 fw-bold text-dark">{{ $project->name }}</h1>
            <p class="text-muted small mb-3">
                Cliente: <strong>{{ $client->name }}</strong>
                @if ($client->company_name) — {{ $client->company_name }} @endif
            </p>
            <div class="d-flex align-items-center gap-3 mt-1">
                <x-badge :status="$project->status" />
                
                <form action="{{ route('admin.clients.projects.destroy', [$client, $project]) }}" method="POST"
                      onsubmit="return confirm('¿Estás seguro de que deseas eliminar este proyecto y todas sus publicaciones de forma permanente?');"
                      class="d-inline-block">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm py-1 px-3 rounded-pill fw-medium" style="font-size: 0.75rem;">
                        <i class="bi bi-trash3 me-1"></i>Eliminar Proyecto
                    </button>
                </form>
            </div>
        </div>
        <div class="text-end bg-light rounded-4 px-4 py-3 border border-secondary border-opacity-10 text-center">
            <span class="display-6 fw-bold text-primary mb-0">{{ $project->progress_percentage }}%</span>
            <p class="small text-muted mb-0 fw-medium text-uppercase letter-spacing-1 mt-1">Completado</p>
        </div>
    </div>
    <div class="mt-4 pt-2">
        <x-progress-bar :percentage="$project->progress_percentage" />
    </div>
</x-card>

<div class="row g-4 mt-2">

    {{-- ── Left: Post form ──────────────────────────────────────────────── --}}
    <div class="col-12 col-xl-5">
        <x-card title="Nueva Publicación" class="shadow-sm border-top border-3 border-primary sticky-top" style="top: 80px">

            <form method="POST"
                  action="{{ route('admin.clients.projects.posts.store', [$client, $project]) }}"
                  enctype="multipart/form-data"
                  id="form-new-post"
                  novalidate>
                @csrf

                <div class="mb-3">
                    <x-input
                        name="title"
                        label="Título de la publicación"
                        :required="true"
                        placeholder="Ej. Avance semana 3 — Inspección submarina"
                    />
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label fw-medium text-dark">
                        Descripción <span class="text-danger" aria-hidden="true">*</span>
                    </label>
                    <textarea
                        id="description"
                        name="description"
                        rows="5"
                        required
                        class="form-control @error('description') is-invalid @enderror"
                        placeholder="Describe el avance, observaciones o hallazgos del proyecto…"
                    >{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <x-input
                        name="published_at"
                        type="datetime-local"
                        label="Fecha de publicación"
                        :value="now()->format('Y-m-d\TH:i')"
                    />
                </div>

                {{-- ── File upload with Vanilla JS preview ──────────────── --}}
                <div class="mb-3">
                    <label for="attachments" class="form-label fw-medium text-dark">
                        Archivos adjuntos
                    </label>
                    <input
                        type="file"
                        id="attachments"
                        name="attachments[]"
                        class="form-control @error('attachments') is-invalid @enderror @error('attachments.*') is-invalid @enderror"
                        multiple
                        accept="image/*,.pdf"
                    >
                    <div class="form-text small">
                        Imágenes (JPG, PNG, GIF, WebP) y PDFs. Máx. 20 MB por archivo.
                    </div>
                    @error('attachments')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    @error('attachments.*')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Preview container (populated by JS) --}}
                <div id="file-preview-container" class="mb-4" aria-live="polite"></div>

                <x-button type="submit" variant="primary" class="w-100 fw-semibold shadow-sm" icon="bi-send">
                    Publicar
                </x-button>

            </form>
        </x-card>
    </div>

    {{-- ── Right: Posts list ────────────────────────────────────────────── --}}
    <div class="col-12 col-xl-7">
        <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
            <h2 class="h5 mb-0 fw-bold text-dark">Publicaciones</h2>
            <span class="badge rounded-pill bg-primary px-3">{{ $project->posts->count() }}</span>
        </div>

        @if ($project->posts->isEmpty())
            <x-alert type="info" class="shadow-sm border-0 bg-white">Aún no hay publicaciones en este proyecto.</x-alert>
        @else
            <div class="d-flex flex-column gap-3">
                @foreach ($project->posts as $post)
                    <x-card class="border-start border-4 border-primary shadow-sm hover-shadow transition-transform">
                        <div class="d-flex align-items-start justify-content-between gap-3 mb-3">
                            <h3 class="h5 mb-0 fw-bold text-dark">{{ $post->title }}</h3>
                            @if ($post->published_at)
                                <span class="small text-muted text-nowrap bg-light px-2 py-1 rounded-3">
                                    <i class="bi bi-calendar3 me-1 text-primary"></i>
                                    {{ $post->published_at->format('d/m/Y H:i') }}
                                </span>
                            @endif
                        </div>
                        <p class="text-muted small mb-4" style="white-space: pre-line; line-height: 1.6;">
                            {{ Str::limit($post->description, 300) }}
                        </p>

                        @if ($post->attachments->count() > 0)
                            <div class="mt-2 pt-3 border-top">
                                <x-attachment-grid :attachments="$post->attachments" :postId="$post->id" />
                            </div>
                        @endif
                    </x-card>
                @endforeach
            </div>
        @endif
    </div>

</div>

{{-- Image viewer modal --}}
<x-modal id="modal-image-viewer" title="Vista de imagen" size="xl">
    <div class="text-center ebt-viewer bg-dark rounded-3 d-flex align-items-center justify-content-center p-2" style="min-height: 200px;">
        <img id="viewer-img" src="" alt="" class="img-fluid rounded" style="max-height: 75vh;">
    </div>
    <x-slot:footer>
        <span class="text-muted small me-auto fw-medium" id="viewer-filename"></span>
        <a id="btn-viewer-download" href="#" download class="btn btn-primary shadow-sm" target="_blank">
            <i class="bi bi-download me-2"></i>Descargar
        </a>
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
    </x-slot:footer>
</x-modal>

@endsection

@push('scripts')
<script type="module">
    import { initImagePreview } from @json(Vite::asset('resources/js/modules/imagePreview.js'));
    import { initImageViewer }  from @json(Vite::asset('resources/js/modules/imageViewer.js'));

    initImagePreview('attachments', 'file-preview-container');
    initImageViewer('modal-image-viewer', 'viewer-img', 'viewer-filename', 'btn-viewer-download');
</script>
<style>
    .hover-shadow { transition: box-shadow 0.2s ease, transform 0.2s ease; }
    .hover-shadow:hover { box-shadow: 0 4px 20px rgba(35, 38, 145, 0.1) !important; transform: translateY(-2px); }
</style>
@endpush
