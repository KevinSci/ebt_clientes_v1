@props([
    'id'          => 'ebt-modal',
    'title'       => 'Modal',
    'size'        => null,      // sm | lg | xl | null (default)
    'scrollable'  => false,
    'centered'    => true,
    'footerClass' => '',
])

@php
    $dialogClass = 'modal-dialog';
    if ($size) $dialogClass .= " modal-{$size}";
    if ($centered) $dialogClass .= ' modal-dialog-centered';
    if ($scrollable) $dialogClass .= ' modal-dialog-scrollable';
@endphp

<div class="modal fade" id="{{ $id }}" tabindex="-1"
     aria-labelledby="{{ $id }}-label" aria-hidden="true">
    <div class="{{ $dialogClass }}">
        <div class="modal-content ebt-modal__content">

            {{-- Header --}}
            <div class="modal-header ebt-modal__header">
                <h5 class="modal-title" id="{{ $id }}-label">{{ $title }}</h5>
                <button type="button" class="btn-close btn-close-white"
                        data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            {{-- Body --}}
            <div class="modal-body ebt-modal__body">
                {{ $slot }}
            </div>

            {{-- Footer (optional named slot) --}}
            @isset($footer)
                <div class="modal-footer ebt-modal__footer {{ $footerClass }}">
                    {{ $footer }}
                </div>
            @endisset

        </div>
    </div>
</div>
