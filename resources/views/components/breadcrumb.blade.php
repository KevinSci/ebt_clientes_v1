@props([
    'items' => [],  {{-- Array of ['label' => '...', 'url' => '...'] --}}
])

@php
    $homeUrl = '#';
    if (auth()->check()) {
        $homeUrl = auth()->user()->isAdmin() 
            ? route('admin.companies.index') 
            : (auth()->user()->companies()->count() > 1 
                ? route('client.dashboard') 
                : (request()->route('company') 
                    ? route('client.companies.projects.index', request()->route('company')) 
                    : '#'));
    }
@endphp

<nav aria-label="breadcrumb" {{ $attributes->merge(['class' => 'mb-3']) }}>
    <ol class="breadcrumb breadcrumb-chevron p-2 p-md-3 bg-body-tertiary rounded-3 align-items-center">
        <li class="breadcrumb-item">
            <a class="link-body-emphasis text-decoration-none" href="{{ $homeUrl }}">
                <i class="bi bi-house-door-fill fs-5"></i>
                <span class="visually-hidden">Inicio</span>
            </a>
        </li>
        @foreach ($items as $index => $item)
            @if ($index === count($items) - 1)
                <li class="breadcrumb-item active" aria-current="page">
                    {{ $item['label'] }}
                </li>
            @else
                <li class="breadcrumb-item">
                    <a class="link-body-emphasis fw-semibold text-decoration-none" href="{{ $item['url'] }}">
                        {{ $item['label'] }}
                    </a>
                </li>
            @endif
        @endforeach
    </ol>
</nav>
