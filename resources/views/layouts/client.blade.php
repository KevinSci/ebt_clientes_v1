@extends('layouts.app')

@section('nav-items')
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('client.projects.*') ? 'active' : '' }}"
           href="{{ route('client.projects.index') }}">
            <i class="bi bi-folder me-1"></i>Mis Proyectos
        </a>
    </li>
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
