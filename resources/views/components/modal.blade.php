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
        <div class="modal-content border-0 shadow-lg">

            {{-- Header --}}
            <div class="modal-header bg-primary text-white border-bottom-0">
                <h5 class="modal-title fw-semibold" id="{{ $id }}-label">{{ $title }}</h5>
                <button type="button" class="btn-close btn-close-white"
                        data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            {{-- Body --}}
            <div class="modal-body p-4">
                {{ $slot }}
            </div>

            {{-- Footer (optional named slot) --}}
            @isset($footer)
                <div class="modal-footer bg-light border-top {{ $footerClass }}">
                    {{ $footer }}
                </div>
            @endisset

        </div>
    </div>
</div>
