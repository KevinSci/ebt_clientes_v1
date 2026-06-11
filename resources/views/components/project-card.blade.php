@props([
    'project',
    'href',
    'linkText' => null,
    'historical' => false,
])

@php
    $statusColor = match ($project->status) {
        'active' => 'primary',
        'paused' => 'warning',
        'completed' => 'success',
        default => 'secondary',
    };
@endphp

<a href="{{ $href }}" class="text-decoration-none text-reset">
    <div {{ $attributes->merge(['class' => "card h-100 border-0 border-start border-3 border-$statusColor"]) }}>
        <div class="card-body">
            <div class="d-flex align-items-start justify-content-between gap-2 mb-1">
                <h3 @class([
                    'h6 mb-0',
                    'fw-bold' => !$historical,
                    'fw-semibold text-muted' => $historical,
                ])>
                    {{ $project->name }}
                </h3>
                <x-badge :status="$project->status" />
            </div>

            <p class="text-muted small mb-3">
                <i class="bi bi-calendar3 me-1"></i>
                @if ($historical)
                    {{ $project->created_at->format('d/m/Y') }}
                @else
                    Iniciado {{ $project->created_at->diffForHumans() }}
                @endif
            </p>

            <x-progress-bar :percentage="$project->progress_percentage" :status="$project->status" />

            @if ($linkText)
                <p class="text-end small text-primary fw-medium mt-2 mb-0">
                    {{ $linkText }} <i class="bi bi-arrow-right ms-1"></i>
                </p>
            @endif
        </div>
    </div>
</a>
