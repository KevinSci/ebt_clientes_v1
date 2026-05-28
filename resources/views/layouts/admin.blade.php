@extends('layouts.app')

@section('nav-items')
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.clients.*') ? 'active' : '' }}"
           href="{{ route('admin.clients.index') }}">
            <i class="bi bi-people me-1"></i>Clientes
        </a>
    </li>
@endsection

@section('content')
    <div class="d-flex flex-column flex-md-row">
        
        {{-- Toggle for mobile sidebar --}}
        <div class="d-md-none bg-dark text-white p-2 d-flex justify-content-between align-items-center border-bottom border-secondary">
            <span class="fw-semibold small text-uppercase">Admin Panel</span>
            <button class="btn btn-sm btn-outline-light" type="button" data-bs-toggle="offcanvas" data-bs-target="#adminSidebar" aria-controls="adminSidebar">
                <i class="bi bi-layout-sidebar"></i> Menú
            </button>
        </div>

        {{-- ── Sidebar (Responsive Offcanvas) ───────────────────────────── --}}
        <aside class="offcanvas-md offcanvas-start bg-dark text-white d-md-flex flex-column flex-shrink-0" tabindex="-1" id="adminSidebar" aria-labelledby="adminSidebarLabel" style="width: 260px; min-height: calc(100vh - 66px);">
            <div class="offcanvas-header border-bottom border-secondary d-md-none">
                <h5 class="offcanvas-title" id="adminSidebarLabel">Admin Panel</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" data-bs-target="#adminSidebar" aria-label="Close"></button>
            </div>
            
            <div class="d-none d-md-block p-4 border-bottom border-secondary border-opacity-25">
                <span class="fs-6 fw-semibold text-uppercase opacity-75 letter-spacing-1">Admin Panel</span>
            </div>

            <div class="offcanvas-body flex-grow-1 p-3">
                <ul class="nav nav-pills flex-column mb-auto">
                    <li class="nav-item">
                        <a href="{{ route('admin.clients.index') }}"
                           class="nav-link text-white d-flex align-items-center gap-3 py-2 px-3 {{ request()->routeIs('admin.clients.*') ? 'active shadow-sm' : '' }}">
                            <i class="bi bi-people-fill fs-5"></i>
                            <span class="fw-medium">Clientes</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="p-3 border-top border-secondary border-opacity-25 mt-auto">
                <div class="d-flex align-items-center gap-3">
                    <span class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width: 36px; height: 36px; font-size: 0.9rem;">
                        {{ mb_strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </span>
                    <div class="text-truncate">
                        <p class="mb-0 fw-semibold small text-white">{{ auth()->user()->name }}</p>
                        <p class="mb-0 text-white-50" style="font-size: .75rem;">Administrador</p>
                    </div>
                </div>
            </div>
        </aside>

        {{-- ── Admin Content Area ───────────────────────────────────────── --}}
        <div class="flex-grow-1 p-4 p-md-5 bg-body w-100 min-w-0">
            @yield('admin-content')
        </div>

    </div>
@endsection
