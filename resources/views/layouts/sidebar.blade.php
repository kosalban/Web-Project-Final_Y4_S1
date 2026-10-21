<aside class="sidebar p-3">

    <div class="sidebar-brand d-flex align-items-center gap-2 fs-6 fw-bold mb-4 px-2">
       <img src="{{ asset('images/Profile_youtube.jpg') }}" alt="{{ __('IT Support Logo') }}" height="60 " width="60">
       <span>KOSAL-IT-Room<small class="d-block text-muted fw-normal">{{ __('Support hardware & software') }}</small></span>
    </div>

    <div class="sidebar-section small px-2 mb-2">{{ __('WORKSPACE') }}</div>
    <nav class="nav flex-column">
        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2 me-2"></i> {{ __('Dashboard') }}
        </a>
        @if(auth()->user()->isAdmin())
            <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <i class="bi bi-people me-2"></i> {{ __('Users') }}
            </a>
        @endif
        <a href="{{ route('technicians.index') }}" class="nav-link {{ request()->routeIs('technicians.*') ? 'active' : '' }}">
            <i class="bi bi-person-gear me-2"></i> {{ __('Technicians') }}
        </a>
        <a href="{{ route('tickets.index') }}" class="nav-link {{ request()->routeIs('tickets.*') ? 'active' : '' }}">
            <i class="bi bi-ticket-detailed me-2"></i> {{ __('Tickets') }}
        </a>
        <a href="{{ route('categories.index') }}" class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
            <i class="bi bi-tags me-2"></i> {{ __('Categories') }}
        </a>
        <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
            <i class="bi bi-bar-chart me-2"></i> {{ __('Reports') }}
        </a>
        <a href="{{ route('settings.edit') }}" class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
            <i class="bi bi-gear me-2"></i> {{ __('Settings') }}
        </a>
    </nav>

    <hr class="border-secondary-subtle">
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button class="nav-link border-0 bg-transparent w-100 text-start">
            <i class="bi bi-box-arrow-right me-2"></i> {{ __('Logout') }}
        </button>
    </form>
</aside>
