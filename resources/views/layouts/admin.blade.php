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
    <div class="d-flex flex-nowrap flex-grow-1">

        {{-- ── Sidebar (Bootstrap 5 official sidebar pattern) ──────────── --}}
        <div class="d-none d-lg-flex flex-column flex-shrink-0 p-3 text-bg-dark ebt-sidebar" style="width: 240px;" id="admin-sidebar">
            <span class="text-uppercase fw-semibold small text-white mb-2">Admin Panel</span>
            <hr>
            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item">
                    <a href="{{ route('admin.clients.index') }}"
                       class="nav-link {{ request()->routeIs('admin.clients.*') ? 'active' : 'text-white' }}">
                        <i class="bi bi-people-fill me-2"></i>
                        Clientes
                    </a>
                </li>
            </ul>
            <hr>
            <div class="d-flex align-items-center gap-2">
                <span class="badge rounded-circle bg-secondary d-inline-flex align-items-center justify-content-center"
                      style="width:28px;height:28px;font-size:.75rem">
                    {{ mb_strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </span>
                <div>
                    <p class="mb-0 fw-semibold small text-white">{{ auth()->user()->name }}</p>
                    <p class="mb-0 text-white-50" style="font-size:.7rem">Administrador</p>
                </div>
            </div>
        </div>

        {{-- ── Admin Content Area ───────────────────────────────────────── --}}
        <div class="flex-grow-1 p-3 p-lg-4" style="min-width:0">
            @yield('admin-content')
        </div>

    </div>
@endsection
