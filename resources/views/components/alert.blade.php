@props([
    'type'        => 'info',   // success | danger | warning | info | primary
    'dismissible' => false,
    'icon'        => null,
])

@php
    $iconMap = [
        'success' => 'bi-check-circle-fill',
        'danger'  => 'bi-exclamation-triangle-fill',
        'warning' => 'bi-exclamation-circle-fill',
        'info'    => 'bi-info-circle-fill',
        'primary' => 'bi-info-circle-fill',
    ];
    $alertIcon = $icon ?? ($iconMap[$type] ?? 'bi-info-circle-fill');
@endphp

<div role="alert"
     {{ $attributes->merge(['class' => 'alert alert-' . $type . ($dismissible ? ' alert-dismissible fade show' : '') . ' d-flex align-items-center gap-2']) }}>

    <i class="bi {{ $alertIcon }} flex-shrink-0" aria-hidden="true"></i>

    <div>{{ $slot }}</div>

    @if ($dismissible)
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    @endif
</div>
