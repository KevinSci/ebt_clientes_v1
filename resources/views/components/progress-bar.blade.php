@props([
    'percentage' => 0,
    'variant'    => null,   // Bootstrap variant override; auto-selected if null
    'showLabel'  => true,
    'height'     => '10',   // px
])

@php
    $pct = max(0, min(100, (int) $percentage));

    if ($variant === null) {
        $variant = match(true) {
            $pct === 100 => 'success',
            $pct >= 60   => 'primary',
            $pct >= 30   => 'warning',
            default      => 'danger',
        };
    }
@endphp

<div class="ebt-progress-wrapper">
    @if ($showLabel)
        <div class="d-flex justify-content-between align-items-center mb-1">
            <span class="small fw-medium text-muted">Progreso</span>
            <span class="small fw-bold text-{{ $variant }}">{{ $pct }}%</span>
        </div>
    @endif

    <div class="progress ebt-progress" style="height: {{ $height }}px" role="progressbar"
         aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100">
        <div class="progress-bar progress-bar-striped progress-bar-animated bg-{{ $variant }} ebt-progress__bar"
             style="width: {{ $pct }}%">
        </div>
    </div>
</div>
