@props([
    'title'    => null,
    'subtitle' => null,
    'footer'   => null,
    'noPad'    => false,
])

<div {{ $attributes->merge(['class' => 'card shadow-sm border-0']) }}>

    @if ($title)
        <div class="card-header bg-light border-bottom py-3 px-4">
            <h5 class="card-title mb-0 fw-semibold">{{ $title }}</h5>
            @if ($subtitle)
                <p class="card-subtitle text-muted small mb-0 mt-1">{{ $subtitle }}</p>
            @endif
        </div>
    @endif

    <div @class(['card-body', 'p-0' => $noPad, 'p-4' => !$noPad])>
        {{ $slot }}
    </div>

    @if ($footer)
        <div class="card-footer bg-light border-top py-3 px-4 text-muted small">
            {{ $footer }}
        </div>
    @endif

</div>
