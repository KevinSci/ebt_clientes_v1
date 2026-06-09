@props([
    'size'    => 'sm',      {{-- xs | sm | md | lg | feed --}}
    'variant' => 'primary', {{-- Bootstrap color variant --}}
    'icon'    => null,      {{-- Bootstrap Icons class, e.g. 'bi-person-fill' --}}
    'text'    => null,      {{-- Text content (e.g. initial letter) --}}
])

<span {{ $attributes->merge(['class' => "badge ebt-avatar ebt-avatar--{$size} bg-{$variant}"]) }}>
    @if ($icon)
        <i class="bi {{ $icon }}" aria-hidden="true"></i>
    @elseif ($text)
        {{ $text }}
    @else
        {{ $slot }}
    @endif
</span>
