@props([
    'title'    => null,
    'subtitle' => null,
    'footer'   => null,
    'noPad'    => false,
])

<div {{ $attributes->merge(['class' => 'card']) }}>

    @if ($title)
        <div class="card-header">
            <h5 class="card-title mb-0">{{ $title }}</h5>
            @if ($subtitle)
                <p class="card-subtitle text-muted small mb-0 mt-1">{{ $subtitle }}</p>
            @endif
        </div>
    @endif

    <div @class(['card-body', 'p-0' => $noPad])>
        {{ $slot }}
    </div>

    @if ($footer)
        <div class="card-footer text-muted small">
            {{ $footer }}
        </div>
    @endif

</div>
