@props([
    'project',
    'href',
    'linkText' => null,
    'historical' => false,
])

@php
    $badgeClasses = match($project->status) {
        'active' => 'rounded-pill px-3 py-2 text-uppercase fw-semibold',
        'paused' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-3 py-2 text-uppercase fw-semibold',
        'completed' => 'bg-success-subtle text-success-emphasis border border-success-subtle rounded-pill px-3 py-2 text-uppercase fw-semibold',
        default => 'rounded-pill px-3 py-2 text-uppercase fw-semibold',
    };

    $linkColor = match ($project->status) {
        'active' => 'text-primary',
        'paused' => 'text-warning-emphasis',
        'completed' => 'text-success',
        default => 'text-secondary',
    };
@endphp

<a href="{{ $href }}" class="text-decoration-none text-reset">
    <div {{ $attributes->merge(['class' => 'card h-100 ebt-project-card']) }}>
        <div class="card-body d-flex flex-column p-4">
            {{-- Top Row --}}
            <div class="d-flex align-items-center justify-content-between mb-3">
                <x-badge :status="$project->status" class="ebt-project-badge-text {{ $badgeClasses }}" />
            </div>

            {{-- Title & Description --}}
            <h3 class="h5 fw-bold text-dark mb-2 mt-2 leading-tight">
                {{ $project->name }}
            </h3>

            {{-- Progress & Info --}}
            <div class="mt-auto">
                <x-progress-bar :percentage="$project->progress_percentage" :status="$project->status" height="6" :showLabel="true" />

                <div class="text-muted mt-2 small">
                    @if ($project->status === 'active')
                        Iniciado {{ $project->created_at->diffForHumans() }}
                    @elseif ($project->status === 'paused')
                        Pausado • Creado hace {{ $project->created_at->diffForHumans() }}
                    @elseif ($project->status === 'completed')
                        Actualizado hace {{ $project->updated_at->diffForHumans() }}
                    @else
                        Actualizado {{ $project->updated_at->diffForHumans() }}
                    @endif
                </div>
            </div>

            {{-- Separator & Footer (Avatars stack / Detail link) --}}
            <div class="d-flex align-items-center justify-content-between mt-3 pt-3 border-top border-light-subtle">
                @php
                    $users = $project->company->users()->take(3)->get();
                    $remainingUsers = max(0, $project->company->users()->count() - 3);
                @endphp
                @if ($project->status === 'active' && $users->isNotEmpty())
                    <div class="ebt-avatar-stack">
                        @foreach($users as $u)
                            <x-avatar size="xs" variant="primary" :text="strtoupper(substr($u->name, 0, 1))" />
                        @endforeach

                        @if($remainingUsers > 0)
                                <x-avatar size="xs" variant="secondary" :text="'+'.$remainingUsers" />
                        @endif
                    </div>
                @else
                    <div></div>
                @endif

                <span class="small {{ $linkColor }} fw-semibold d-flex align-items-center gap-1">
                    {{ $linkText ?? 'Ver proyecto' }} <i class="bi bi-arrow-right"></i>
                </span>
            </div>
        </div>
    </div>
</a>
