@props([
    'company',
    'url' => null,
])

@php
    $words = explode(' ', $company->name);
    $initials = '';
    if (count($words) >= 2) {
        $initials = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
    } else {
        $initials = strtoupper(substr($company->name, 0, 2));
    }

    // Deterministic gradient selection based on company ID
    $gradients = [
        'linear-gradient(135deg, #7F00FF 0%, #E100FF 100%)', // Purple/Pink
        'linear-gradient(135deg, #00C6FF 0%, #0072FF 100%)', // Blue
        'linear-gradient(135deg, #02AAB0 0%, #00CDAC 100%)', // Teal/Green
        'linear-gradient(135deg, #FF512F 0%, #DD2476 100%)', // Orange/Red
        'linear-gradient(135deg, #11998e 0%, #38ef7d 100%)', // Emerald/Teal
    ];
    $gradient = $gradients[$company->id % count($gradients)];
    
    $activeProjects = $company->activeProjectsCount();
    $totalProjects = $company->projects()->count();
    $lastModified = $company->lastModifiedDate();
@endphp

<div class="card h-100 border-0 shadow-sm rounded-2 p-3 text-center hover-shadow transition-all duration-200 position-relative">
    {{-- Avatar Circular with Gradient --}}
    <div class="d-flex justify-content-center mb-2">
        <x-avatar size="md" :text="$initials" style="width: 60px; height: 60px; font-size: 1.3rem; background: {{ $gradient }} !important; border: 2px solid #fff; outline: 1px solid #e2e8f0;" class="shadow-sm" />
    </div>

    {{-- Company Title --}}
    <h5 class="fw-bold mb-1 text-dark text-truncate" title="{{ $company->name }}">
        {{ $company->name }}
    </h5>

    {{-- Subtitle (Location or RFC) --}}
    <div class="text-muted small mb-2 text-truncate">
        @if($company->address)
            <i class="bi bi-geo-alt-fill me-1 text-secondary"></i>{{ $company->address }}
        @else
            <i class="bi bi-building me-1 text-secondary"></i>Empresa comercial
        @endif
    </div>

    <hr class="my-2 opacity-25">

    {{-- Stats Row --}}
    <div class="row g-0 mb-3 align-items-start">
        <div class="col-4">
            <div class="fw-bold fs-6 text-dark lh-sm">{{ $activeProjects }}</div>
            <div class="small text-uppercase text-muted lh-sm" style="font-size: 0.7rem;">Activos</div>
        </div>
        <div class="col-4 border-start border-end">
            <div class="fw-bold fs-6 text-dark lh-sm">{{ $totalProjects }}</div>
            <div class="small text-uppercase text-muted lh-sm" style="font-size: 0.7rem;">Proyectos</div>
        </div>
        <div class="col-4">
            <div class="fw-bold fs-6 text-dark text-truncate lh-sm" title="{{ $lastModified ? $lastModified->format('d/m/Y H:i') : 'N/A' }}">
                {{ $lastModified ? $lastModified->diffForHumans(null, true, true) : 'N/A' }}
            </div>
            <div class="small text-uppercase text-muted lh-sm" style="font-size: 0.7rem;">Modificado</div>
        </div>
    </div>

    {{-- Actions --}}
    <div class="mt-auto">
        <x-button :href="$url ?? route('client.companies.projects.index', $company)" variant="dark" class="w-100 fw-semibold py-1.5 shadow-sm d-flex align-items-center justify-content-center gap-1 rounded-1">
            Ingresar <i class="bi bi-arrow-right-short fs-5"></i>
        </x-button>
    </div>
</div>
