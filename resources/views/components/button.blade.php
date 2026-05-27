@props([
    'variant' => 'primary',   // Bootstrap color variant
    'type'    => 'button',    // button | submit | reset
    'href'    => null,        // If set, renders as <a>
    'size'    => null,        // sm | lg | null
    'outline' => false,
    'icon'    => null,        // Bootstrap Icons class, e.g. 'bi-plus'
])

@php
    $btnClass = $outline
        ? "btn btn-outline-{$variant}"
        : "btn btn-{$variant}";

    if ($size) {
        $btnClass .= " btn-{$size}";
    }
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $btnClass]) }}>
        @if ($icon)<i class="{{ $icon }} me-1"></i>@endif
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $btnClass]) }}>
        @if ($icon)<i class="{{ $icon }} me-1"></i>@endif
        {{ $slot }}
    </button>
@endif
