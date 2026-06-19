@props([
    'attachments' => collect(),
    'postId'      => null,
    'maxVisible'  => 4,
])

@php
    // Agrupar archivos que pertenecen a carpetas
    $folderGroups = $attachments->filter(fn ($a) => $a->folder_name !== null)->groupBy('folder_name');

    // Archivos individuales (sin carpeta)
    $individualAttachments = $attachments->filter(fn ($a) => $a->folder_name === null);
    $images    = $individualAttachments->filter(fn ($a) => $a->isImage());
    $documents = $individualAttachments->filter(fn ($a) => $a->isDocument());
    $allImages = $images->values();
    $visible   = $allImages->take($maxVisible);
    $overflow  = $allImages->count() - $maxVisible;
@endphp

{{-- ── Folder attachments ────────────────────────────────────────────── --}}
@if ($folderGroups->count() > 0)
    <div class="mb-3">
        <p class="small fw-semibold text-muted mb-2">
            <i class="bi bi-folder2-open me-1"></i>
            {{ $folderGroups->count() }} {{ Str::plural('carpeta', $folderGroups->count()) }}
        </p>
        <div class="d-flex flex-wrap gap-2 mb-2">
            @foreach ($folderGroups as $folderName => $files)
                @php
                    $filesJson = $files->map(fn($f) => [
                        'id' => $f->id,
                        'file_name' => $f->file_name,
                        'file_path' => $f->file_path,
                        'url' => $f->url,
                        'type' => $f->type,
                        'is_pdf' => $f->isPdf(),
                        'icon' => $f->icon,
                        'display_path' => $f->getRelativeDisplayPath(),
                        'folder_path' => $f->folder_path,
                    ])->values()->toJson();
                @endphp
                <button type="button" 
                        class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center gap-2 py-1.5 px-3 ebt-folder-chip ebt-folder-trigger"
                        data-folder-name="{{ $folderName }}"
                        data-files="{{ $filesJson }}">
                    <i class="bi bi-folder-fill text-warning fs-5"></i>
                    <span class="fw-medium text-dark text-truncate" style="max-width: 180px;">{{ $folderName }}</span>
                    <span class="badge bg-secondary rounded-pill">{{ $files->count() }}</span>
                </button>
            @endforeach
        </div>
    </div>
@endif

{{-- ── Document attachments ────────────────────────────────────────────── --}}
@if ($documents->count() > 0)
    <div class="mb-3">
        <p class="small fw-semibold text-muted mb-2">
            <i class="bi bi-paperclip me-1"></i>
            {{ $documents->count() }} {{ Str::plural('documento', $documents->count()) }}
        </p>
        <div class="list-group">
            @foreach ($documents as $doc)
                @php
                    $isPdf = $doc->isPdf();
                @endphp
                <a href="{{ $doc->url }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="list-group-item list-group-item-action d-flex align-items-center gap-2 {{ $isPdf ? 'ebt-pdf-link' : '' }}"
                   @if($isPdf)
                       data-file-path="{{ $doc->file_path }}"
                       data-file-name="{{ $doc->file_name }}"
                   @endif>
                    <i class="bi {{ $doc->icon['icon'] }} {{ $doc->icon['color'] }}"></i>
                    <span class="text-truncate flex-grow-1">{{ $doc->file_name }}</span>
                    <i class="bi bi-download text-muted"></i>
                </a>
            @endforeach
        </div>
    </div>
@endif

{{-- ── Image grid (Facebook-style) — kept as high customization exception ── --}}
@if ($allImages->count() > 0)
    <div class="ebt-img-grid ebt-img-grid--{{ min($allImages->count(), $maxVisible) }}"
         data-post-id="{{ $postId }}">

        @foreach ($visible as $index => $image)
            @php
                $isLast        = $index === $maxVisible - 1;
                $hasOverflow   = $overflow > 0 && $isLast;
            @endphp

            <div class="ebt-img-grid__item">
                <img
                    src="{{ $image->url }}"
                    alt="{{ $image->file_name }}"
                    class="ebt-img-grid__img ebt-viewer-trigger"
                    data-src="{{ $image->url }}"
                    data-filename="{{ $image->file_name }}"
                    data-images="{{ $allImages->pluck('url')->toJson() }}"
                    data-filenames="{{ $allImages->pluck('file_name')->toJson() }}"
                    data-index="{{ $index }}"
                    loading="lazy"
                >
                @if ($hasOverflow)
                    <div class="ebt-img-grid__overlay" aria-hidden="true">
                        <span>+{{ $overflow }}</span>
                    </div>
                @endif
            </div>
        @endforeach

    </div>
@endif
