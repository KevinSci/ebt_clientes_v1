@extends('layouts.app')

@section('nav-items')
    <li class="nav-item d-lg-none">
        <a class="nav-link {{ request()->routeIs('admin.companies.*') ? 'active' : '' }}"
           href="{{ route('admin.companies.index') }}">
            <i class="bi bi-building me-1"></i>Empresas
        </a>
    </li>
    <li class="nav-item d-lg-none">
        <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
           href="{{ route('admin.users.index') }}">
            <i class="bi bi-people me-1"></i>Usuarios
        </a>
    </li>
@endsection

@section('footer')
    {{-- Override global footer in admin layout --}}
@endsection

@section('content')
    <div class="d-flex flex-nowrap flex-grow-1">

        {{-- ── Sidebar (Bootstrap 5 official sidebar pattern) ──────────── --}}
        <div class="d-none d-lg-flex flex-column flex-shrink-0 p-3 text-bg-dark ebt-sidebar" id="admin-sidebar">
            <span class="text-uppercase fw-semibold small text-white mb-2">Admin Panel</span>
            <hr>
            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item">
                    <a href="{{ route('admin.companies.index') }}"
                       class="nav-link {{ request()->routeIs('admin.companies.*') ? 'active' : 'text-white' }} mb-2">
                        <i class="bi bi-building-fill me-2"></i>
                        Empresas
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.users.index') }}"
                       class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : 'text-white' }}">
                        <i class="bi bi-people-fill me-2"></i>
                        Usuarios
                    </a>
                </li>
            </ul>
            <hr>
            <a href="{{ route('admin.profile.edit') }}" class="d-flex align-items-center gap-2 text-decoration-none text-white opacity-85 hover-opacity-100 transition-opacity">
                <i class="bi bi-gear-fill"></i>
                <span class="small fw-semibold">Configuración</span>
            </a>
        </div>

        {{-- ── Admin Content Area & Footer ───────────────────────────────── --}}
        <div class="d-flex flex-column flex-grow-1 min-w-0 ebt-admin-content-wrapper">
            <div class="flex-grow-1 p-3 p-lg-4">
                @yield('admin-content')
            </div>

            <footer class="bg-dark text-light py-3 mt-auto">
                <div class="container-fluid text-center">
                    <p class="mb-1 fw-semibold">
                        <span class="text-danger">EBT</span> Servicios Profesionales
                    </p>
                    <p class="small text-secondary mb-0">
                        Portal de Clientes &copy; {{ date('Y') }} — Todos los derechos reservados
                    </p>
                </div>
            </footer>
        </div>

    </div>
@endsection
