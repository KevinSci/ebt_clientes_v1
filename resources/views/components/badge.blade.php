@props([
    'status' => 'active',
])

@php
    $map = [
        'active'    => ['variant' => 'primary',   'label' => 'Activo',     'icon' => 'bi-circle-fill'],
        'paused'    => ['variant' => 'warning',   'label' => 'Pausado',    'icon' => 'bi-pause-circle-fill'],
        'completed' => ['variant' => 'success',   'label' => 'Completado', 'icon' => 'bi-check-circle-fill'],
    ];

    $config = $map[$status] ?? ['variant' => 'light', 'label' => ucfirst($status), 'icon' => 'bi-circle'];
@endphp

<span {{ $attributes->merge(['class' => 'badge bg-' . $config['variant']]) }}>
    <i class="bi {{ $config['icon'] }} me-1" aria-hidden="true"></i>
    {{ $config['label'] }}
</span>
