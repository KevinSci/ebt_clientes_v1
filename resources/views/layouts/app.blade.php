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
<body class="d-flex flex-column min-vh-100 bg-light overflow-x-hidden">

    {{-- ── Navbar ──────────────────────────────────────────────────────── --}}
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top" id="ebt-navbar">
        <div class="container-fluid px-3">

            {{-- Brand --}}
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('login') }}">
                <img src="{{ asset('img/logo.svg') }}" alt="EBT Logo" class="ebt-logo-navbar">
                <span class="small text-white d-none d-sm-inline">Servicios Profesionales</span>
            </a>

            {{-- Mobile toggle --}}
            <button class="navbar-toggler" type="button"
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
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2"
                           href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <x-avatar size="sm" variant="danger" :text="mb_strtoupper(substr(auth()->user()->name, 0, 1))" />
                            <span class="d-none d-md-inline fw-medium">{{ auth()->user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <span class="dropdown-item-text small text-muted">
                                    {{ auth()->user()->company_name ?? auth()->user()->email }}
                                </span>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
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

    {{-- ── Flash Messages (Bootstrap Toast) ────────────────────────────── --}}
    @if (session('success') || session('error'))
        <div class="toast-container position-fixed end-0 p-3 ebt-toast-container">
            
            <template id="toast-template">
                @if (session('success'))
                    <div class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="d-flex">
                            <div class="toast-body">
                                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Cerrar"></button>
                        </div>
                    </div>
                @endif

                @if (session('error'))
                    <div class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="d-flex">
                            <div class="toast-body">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Cerrar"></button>
                        </div>
                    </div>
                @endif
            </template>
        </div>
    @endif

    {{-- ── Main Content ─────────────────────────────────────────────────── --}}
    <main id="main-content" class="d-flex flex-column flex-grow-1">
        @yield('content')
    </main>

    {{-- ── Footer ──────────────────────────────────────────────────────── --}}
    @section('footer')
        <footer class="bg-dark text-light py-3 mt-auto">
            <div class="container text-center">
                <p class="mb-1 fw-semibold">
                    <span class="text-danger">EBT</span> Servicios Profesionales
                </p>
                <p class="small text-secondary mb-0">
                    Portal de Clientes &copy; {{ date('Y') }} — Todos los derechos reservados
                </p>
            </div>
        </footer>
    @show

    @stack('scripts')

</body>
</html>
