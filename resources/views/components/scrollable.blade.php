@props([
    'maxHeight' => '400px',
])

<div {{ $attributes->merge(['class' => 'ebt-scrollable-container']) }} style="--ebt-scroll-max-height: {{ $maxHeight }};">
    <div class="pe-lg-2">
        {{ $slot }}
    </div>
</div>
