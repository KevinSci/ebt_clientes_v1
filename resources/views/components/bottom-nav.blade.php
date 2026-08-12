@auth
    @php
        $isAdmin = auth()->user()->isAdmin();
    @endphp

    <div class="fixed-bottom bg-white border-top d-flex d-lg-none justify-content-around py-2 shadow-sm ebt-bottom-nav" id="ebt-bottom-nav">
        @if($isAdmin)
            {{-- Admin Menu --}}
            <a href="{{ route('admin.companies.index') }}" 
               class="nav-link text-center flex-fill {{ request()->routeIs('admin.companies.*') ? 'active' : '' }}">
                <i class="bi {{ request()->routeIs('admin.companies.*') ? 'bi-building-fill' : 'bi-building' }} fs-5"></i>
                <span class="ebt-bottom-nav-text">Empresas</span>
            </a>

            <a href="{{ route('admin.users.index') }}" 
               class="nav-link text-center flex-fill {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="bi {{ request()->routeIs('admin.users.*') ? 'bi-people-fill' : 'bi-people' }} fs-5"></i>
                <span class="ebt-bottom-nav-text">Usuarios</span>
            </a>

            <a href="{{ route('admin.profile.edit') }}" 
               class="nav-link text-center flex-fill {{ request()->routeIs('admin.profile.*') ? 'active' : '' }}">
                <i class="bi {{ request()->routeIs('admin.profile.*') ? 'bi-gear-fill' : 'bi-gear' }} fs-5"></i>
                <span class="ebt-bottom-nav-text">Ajustes</span>
            </a>
        @else
            {{-- Client Menu --}}
            @php
                $company = request()->route('company');
            @endphp

                <a 
                @if ($company)
                    href="{{ route('client.companies.projects.index', $company) }}" class="nav-link text-center flex-fill active">
                    <i class="bi bi-folder-fill fs-5"></i>
                    <span class="ebt-bottom-nav-text">Proyectos</span>
                @else
                    href="#" class="nav-link text-center flex-fill disabled">
                    <i class="bi bi-folder fs-5 opacity-50"></i>
                    <span class="ebt-bottom-nav-text opacity-50">Proyectos</span>
                @endif
                </a>

            @if(auth()->user()->hasMultipleCompanies())
                <a href="{{ route('client.dashboard') }}" 
                   class="nav-link text-center flex-fill {{ request()->routeIs('client.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-arrow-left-right fs-5"></i>
                    <span class="ebt-bottom-nav-text">Empresas</span>
                </a>
            @endif

            <a href="{{ route('client.profile.edit') }}" 
               class="nav-link text-center flex-fill {{ request()->routeIs('client.profile.*') ? 'active' : '' }}">
                <i class="bi {{ request()->routeIs('client.profile.*') ? 'bi-gear-fill' : 'bi-gear' }} fs-5"></i>
                <span class="ebt-bottom-nav-text">Ajustes</span>
            </a>
        @endif
    </div>
@endauth
