@props([
    'attachments' => collect(),
    'postId'      => null,
    'maxVisible'  => 4,
])

@php
    $images    = $attachments->filter(fn ($a) => $a->isImage());
    $documents = $attachments->filter(fn ($a) => $a->isDocument());
    $allImages = $images->values();
    $visible   = $allImages->take($maxVisible);
    $overflow  = $allImages->count() - $maxVisible;
@endphp

{{-- ── Document attachments ────────────────────────────────────────────── --}}
@if ($documents->count() > 0)
    <div class="mb-3">
        <p class="small fw-semibold text-muted mb-2">
            <i class="bi bi-paperclip me-1"></i>
            {{ $documents->count() }} {{ Str::plural('documento', $documents->count()) }}
        </p>
        <div class="list-group">
            @foreach ($documents as $doc)
                <a href="{{ $doc->url }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="list-group-item list-group-item-action d-flex align-items-center gap-2">
                    <i class="bi bi-file-earmark-pdf-fill text-danger"></i>
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
