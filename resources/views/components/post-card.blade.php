@props([
    'post',
    'canEdit' => false,
    'editModalTarget' => null,
    'editUpdateUrl' => null,
    'editDeleteUrl' => null,
])

@php
    $isSelf = false;
    if (auth()->user()->isAdmin()) {
        if (is_null($post->user_id) || $post->user_id === auth()->id()) {
            $isSelf = true;
        }
    } else {
        if ($post->user_id === auth()->id()) {
            $isSelf = true;
        }
    }
    $cardClasses = 'card mb-3' . ($isSelf ? ' bg-info bg-opacity-10 border-info-subtle' : '');
    $target = $editModalTarget ?? '#modal-edit-post-' . $post->id;

    $attachmentsJson = $post->relationLoaded('attachments') ? $post->attachments->map(fn($a) => [
        'id'          => $a->id,
        'file_name'   => $a->file_name,
        'file_path'   => $a->file_path,
        'url'         => $a->url,
        'type'        => $a->type,
        'folder_name' => $a->folder_name,
        'icon'        => $a->icon,
    ])->values()->toJson() : '[]';
@endphp

<div class="{{ $cardClasses }}" data-post-id="{{ $post->id }}">
    <div class="card-body">
        <div class="d-flex align-items-start justify-content-between gap-3 mb-2">
            <div class="min-w-0 flex-grow-1">
                <h3 class="h6 mb-1 fw-bold text-dark text-break">{{ $post->title }}</h3>
                <div class="d-flex flex-wrap align-items-center gap-2 small text-muted">
                    <span>
                        <i class="bi bi-person me-1"></i>
                        {{ $post->author ? $post->author->name : 'EBT Consultores' }}
                    </span>
                    @if ($post->published_at)
                        <span>•</span>
                        <time datetime="{{ $post->published_at->toIso8601String() }}" class="text-nowrap">
                            <i class="bi bi-calendar3 me-1" aria-hidden="true"></i>
                            {{ $post->published_at->format('d/m/Y H:i') }}
                        </time>
                    @endif
                </div>
            </div>
            @if ($canEdit)
                <div class="flex-shrink-0 ms-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm py-1 px-2 d-flex align-items-center gap-1"
                            data-bs-toggle="modal"
                            data-bs-target="{{ $target }}"
                            data-post-id="{{ $post->id }}"
                            data-post-title="{{ $post->title }}"
                            data-post-description="{{ $post->description }}"
                            data-post-published-at="{{ $post->published_at ? $post->published_at->format('Y-m-d\TH:i') : '' }}"
                            data-update-url="{{ $editUpdateUrl }}"
                            data-delete-url="{{ $editDeleteUrl }}"
                            data-attachments='{{ $attachmentsJson }}'>
                        <i class="bi bi-pencil"></i>
                        <span class="d-none d-sm-inline">Editar</span>
                    </button>
                </div>
            @endif
        </div>
        <div class="mb-3">
            <x-read-more :text="$post->description" class="small" />
        </div>

        @if ($post->attachments->count() > 0)
            <x-attachment-grid :attachments="$post->attachments" :postId="$post->id" />
        @endif
    </div>
</div>
