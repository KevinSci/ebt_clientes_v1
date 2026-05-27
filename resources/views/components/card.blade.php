@props([
    'title'    => null,
    'subtitle' => null,
    'footer'   => null,
    'noPad'    => false,
])

<div {{ $attributes->merge(['class' => 'card ebt-card']) }}>

    @if ($title)
        <div class="card-header ebt-card__header">
            <h5 class="card-title mb-0">{{ $title }}</h5>
            @if ($subtitle)
                <p class="card-subtitle text-muted small mb-0 mt-1">{{ $subtitle }}</p>
            @endif
        </div>
    @endif

    <div @class(['card-body ebt-card__body', 'p-0' => $noPad])>
        {{ $slot }}
    </div>

    @if ($footer)
        <div class="card-footer ebt-card__footer text-muted small">
            {{ $footer }}
        </div>
    @endif

</div>
