<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Portal EBT') — EBT Servicios Profesionales</title>
    <meta name="description" content="@yield('meta_description', 'Portal de Clientes EBT — Gestión de proyectos y evidencias.')">

    {{-- Bootstrap CSS vía Vite (SCSS compilado) --}}
    @vite(['resources/css/bootstrap.scss', 'resources/css/app.css', 'resources/js/app.js'])

    @stack('head')
</head>
<body class="bg-light text-dark d-flex flex-column min-vh-100">

    {{-- ── Navbar ──────────────────────────────────────────────────────── --}}
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm py-2" id="ebt-navbar">
        <div class="container-fluid px-4">

            {{-- Brand --}}
            <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="{{ route('login') }}">
                <img src="{{ Vite::asset('resources/img/logo.svg') }}" alt="EBT Logo" class="ebt-logo-img" style="height: 40px;">
                <span class="text-white fw-semibold small d-none d-sm-inline opacity-75">Servicios Profesionales</span>
            </a>

            {{-- Mobile toggle --}}
            <button class="navbar-toggler border-0" type="button"
                    data-bs-toggle="collapse" data-bs-target="#ebtNavMenu"
                    aria-controls="ebtNavMenu" aria-expanded="false" aria-label="Abrir menú">
                <span class="navbar-toggler-icon"></span>
            </button>

            {{-- Nav items --}}
            <div class="collapse navbar-collapse" id="ebtNavMenu">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-1">
                    @yield('nav-items')

                    @auth
                    <li class="nav-item dropdown ms-lg-3">
                        <a class="nav-link dropdown-toggle text-white d-flex align-items-center gap-2"
                           href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 36px; height: 36px; font-size: 0.9rem;">
                                {{ mb_strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </span>
                            <span class="d-none d-md-inline fw-medium">{{ auth()->user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 rounded-3">
                            <li>
                                <span class="dropdown-item-text small text-muted">
                                    {{ auth()->user()->company_name ?? auth()->user()->email }}
                                </span>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger fw-medium d-flex align-items-center gap-2">
                                        <i class="bi bi-box-arrow-right"></i>Cerrar sesión
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                    @endauth
                </ul>
            </div>

        </div>
    </nav>

    {{-- ── Flash Messages ──────────────────────────────────────────────── --}}
    @if (session('success'))
        <div class="ebt-flash-container">
            <x-alert type="success" :dismissible="true">{{ session('success') }}</x-alert>
        </div>
    @endif
    @if (session('error'))
        <div class="ebt-flash-container">
            <x-alert type="danger" :dismissible="true">{{ session('error') }}</x-alert>
        </div>
    @endif

    {{-- ── Main Content ─────────────────────────────────────────────────── --}}
    <main id="main-content" class="flex-grow-1">
        @yield('content')
    </main>

    {{-- ── Footer ──────────────────────────────────────────────────────── --}}
    <footer class="bg-dark text-white-50 py-4 mt-auto">
        <div class="container text-center">
            <p class="mb-1 fw-semibold text-white">
                <span class="text-danger">EBT</span> Servicios Profesionales
            </p>
            <p class="small mb-0 opacity-75">
                Portal de Clientes &copy; {{ date('Y') }} — Todos los derechos reservados
            </p>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
