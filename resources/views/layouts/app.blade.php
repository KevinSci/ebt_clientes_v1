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
<body class="ebt-body">

    {{-- ── Navbar ──────────────────────────────────────────────────────── --}}
    <nav class="navbar navbar-expand-lg ebt-navbar" id="ebt-navbar">
        <div class="container-fluid px-4">

            {{-- Brand --}}
            <a class="navbar-brand ebt-navbar__brand d-flex align-items-center gap-2" href="{{ route('login') }}">
                <img src="{{ asset('img/logo.svg') }}" alt="EBT Logo" class="ebt-logo-img ebt-logo-img--navbar">
                <span class="ebt-logo-text d-none d-sm-inline">Servicios Profesionales</span>
            </a>

            {{-- Mobile toggle --}}
            <button class="navbar-toggler ebt-navbar__toggler" type="button"
                    data-bs-toggle="collapse" data-bs-target="#ebtNavMenu"
                    aria-controls="ebtNavMenu" aria-expanded="false" aria-label="Abrir menú">
                <span class="navbar-toggler-icon"></span>
            </button>

            {{-- Nav items --}}
            <div class="collapse navbar-collapse" id="ebtNavMenu">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-1">
                    @yield('nav-items')

                    @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle ebt-navbar__user d-flex align-items-center gap-2"
                           href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="ebt-avatar">{{ mb_strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                            <span class="d-none d-md-inline fw-medium">{{ auth()->user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end ebt-dropdown">
                            <li>
                                <span class="dropdown-item-text small text-muted">
                                    {{ auth()->user()->company_name ?? auth()->user()->email }}
                                </span>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item ebt-dropdown__logout">
                                        <i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión
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
    <main id="main-content" class="ebt-main">
        @yield('content')
    </main>

    {{-- ── Footer ──────────────────────────────────────────────────────── --}}
    <footer class="ebt-footer">
        <div class="container text-center">
            <p class="mb-1 fw-semibold">
                <span class="text-ebt-red">EBT</span> Servicios Profesionales
            </p>
            <p class="small text-muted mb-0">
                Portal de Clientes &copy; {{ date('Y') }} — Todos los derechos reservados
            </p>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
