@extends('layouts.app')

@section('nav-items')
    @php
        $company = request()->route('company');
    @endphp
    @if ($company)
        <li class="nav-item d-lg-none">
            <a class="nav-link {{ request()->routeIs('client.companies.projects.*') ? 'active' : '' }}"
               href="{{ route('client.companies.projects.index', $company) }}">
                <i class="bi bi-folder me-1"></i>Mis Proyectos
            </a>
        </li>
    @endif
    @if (auth()->check() && auth()->user()->companies()->count() > 1)
        <li class="nav-item d-lg-none">
            <a class="nav-link {{ request()->routeIs('client.dashboard') ? 'active' : '' }}" 
               href="{{ route('client.dashboard') }}">
                <i class="bi bi-arrow-left-right me-1"></i>Cambiar Empresa
            </a>
        </li>
    @endif
@endsection

@section('content')
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10 col-xl-9">
                @yield('client-content')
            </div>
        </div>
    </div>
@endsection
