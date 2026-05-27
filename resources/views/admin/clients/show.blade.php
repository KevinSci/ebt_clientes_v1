@extends('layouts.admin')

@section('title', $client->name . ' — Cliente')

@section('admin-content')

{{-- Breadcrumb --}}
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb ebt-breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('admin.clients.index') }}">Clientes</a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">{{ $client->name }}</li>
    </ol>
</nav>

{{-- ── Client profile header ───────────────────────────────────────────── --}}
<x-card class="mb-4 ebt-profile-card">
    <div class="d-flex flex-wrap align-items-center gap-4">
        <span class="ebt-avatar ebt-avatar--xl flex-shrink-0">
            {{ mb_strtoupper(substr($client->name, 0, 1)) }}
        </span>
        <div class="flex-grow-1">
            <h1 class="h4 mb-1 fw-bold">{{ $client->name }}</h1>
            @if ($client->company_name)
                <p class="mb-1 text-muted">
                    <i class="bi bi-building me-2"></i>{{ $client->company_name }}
                </p>
            @endif
            <p class="mb-1 text-muted small">
                <i class="bi bi-envelope me-2"></i>{{ $client->email }}
            </p>
            @if ($client->phone)
                <p class="mb-0 text-muted small">
                    <i class="bi bi-telephone me-2"></i>{{ $client->phone }}
                </p>
            @endif
        </div>
        <div class="text-center ebt-profile-card__stat">
            <p class="display-6 fw-bold text-primary mb-0">{{ $client->projects->count() }}</p>
            <p class="small text-muted mb-0">{{ Str::plural('Proyecto', $client->projects->count()) }}</p>
        </div>
    </div>
</x-card>

{{-- ── Projects list ───────────────────────────────────────────────────── --}}
<div class="d-flex align-items-center justify-content-between mb-3">
    <h2 class="h5 mb-0">Proyectos</h2>
</div>

@if ($client->projects->isEmpty())
    <x-alert type="info">Este cliente no tiene proyectos aún.</x-alert>
@else
    <div class="row g-3">
        @foreach ($client->projects as $project)
            <div class="col-12 col-lg-6">
                <a href="{{ route('admin.clients.projects.show', [$client, $project]) }}"
                   class="text-decoration-none ebt-project-card-link">
                    <x-card class="ebt-project-card h-100">
                        <div class="d-flex align-items-start justify-content-between gap-2 mb-3">
                            <h3 class="h6 mb-0 fw-semibold">{{ $project->name }}</h3>
                            <x-badge :status="$project->status" />
                        </div>
                        <x-progress-bar :percentage="$project->progress_percentage" />
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <span class="small text-muted">
                                <i class="bi bi-calendar3 me-1"></i>
                                {{ $project->created_at->format('d/m/Y') }}
                            </span>
                            <span class="small text-primary fw-medium">
                                Ver proyecto <i class="bi bi-arrow-right ms-1"></i>
                            </span>
                        </div>
                    </x-card>
                </a>
            </div>
        @endforeach
    </div>
@endif

@endsection
