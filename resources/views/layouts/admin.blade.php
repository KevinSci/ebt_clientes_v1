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
    <div class="ebt-admin-wrapper">

        {{-- ── Sidebar ──────────────────────────────────────────────────── --}}
        <aside class="ebt-sidebar" id="admin-sidebar">
            <div class="ebt-sidebar__header">
                <div class="ebt-sidebar__logo">
                    <span class="ebt-logo-mark ebt-logo-mark--sm">EBT</span>
                    <span class="ebt-sidebar__title">Admin Panel</span>
                </div>
            </div>

            <nav class="ebt-sidebar__nav" aria-label="Menú de administración">
                <ul class="list-unstyled mb-0">
                    <li>
                        <a href="{{ route('admin.clients.index') }}"
                           class="ebt-sidebar__link {{ request()->routeIs('admin.clients.*') ? 'is-active' : '' }}">
                            <i class="bi bi-people-fill"></i>
                            <span>Clientes</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="ebt-sidebar__footer">
                <div class="ebt-sidebar__user">
                    <span class="ebt-avatar ebt-avatar--sm">
                        {{ mb_strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </span>
                    <div class="ebt-sidebar__user-info">
                        <p class="mb-0 fw-semibold small">{{ auth()->user()->name }}</p>
                        <p class="mb-0 text-muted" style="font-size:.7rem">Administrador</p>
                    </div>
                </div>
            </div>
        </aside>

        {{-- ── Admin Content Area ───────────────────────────────────────── --}}
        <div class="ebt-admin-content">
            @yield('admin-content')
        </div>

    </div>
@endsection
