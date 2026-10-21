<nav class="navbar topbar px-3 px-lg-4 py-3">
    <div class="container-fluid p-0 d-flex justify-content-between">
        <div>
            <span class="navbar-brand fw-bold fs-4 d-block mb-0">@yield('page-heading', 'Dashboard')</span>
            <span class="topbar-subtitle">{{ __('Welcome back, :name', ['name' => auth()->user()->name]) }}</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('tickets.index') }}" class="btn btn-sm btn-outline-light d-none d-md-inline-flex align-items-center">
                <i class="bi bi-ticket-detailed me-2"></i>{{ __('Tickets') }}
            </a>
            @can('create-tickets')
                <a href="{{ route('tickets.create') }}" class="btn btn-sm btn-light text-primary fw-semibold d-none d-sm-inline-flex align-items-center">
                    <i class="bi bi-plus-lg me-1"></i>{{ __('New Ticket') }}
                </a>
            @endcan
            <div class="dropdown">
                <button class="btn btn-outline-light position-relative" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="{{ __('Notifications') }}">
                    <i class="bi bi-bell"></i>
                    @if($unreadNotificationCount > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">{{ $unreadNotificationCount }}</span>
                    @endif
                </button>
                <ul class="dropdown-menu dropdown-menu-end p-2" style="width:min(360px, 90vw)">
                    <li class="d-flex justify-content-between align-items-center px-2 py-1">
                        <span class="fw-semibold">{{ __('Notifications') }}</span>
                        @if($unreadNotificationCount > 0)
                            <form method="POST" action="{{ route('notifications.read-all') }}">
                                @csrf
                                <button class="btn btn-sm btn-link text-decoration-none p-0">{{ __('Mark all read') }}</button>
                            </form>
                        @endif
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    @forelse($latestNotifications as $notification)
                        <li>
                            <form method="POST" action="{{ route('notifications.open', $notification->id) }}">
                                @csrf
                                <button class="dropdown-item text-wrap rounded-2" type="submit">
                                    <span class="d-block fw-semibold">{{ __($notification->data['title']) }}</span>
                                    <span class="d-block small text-muted">{{ $notification->data['message'] }}</span>
                                    <span class="d-block small text-muted mt-1">{{ $notification->created_at->locale(app()->getLocale())->diffForHumans() }}</span>
                                </button>
                            </form>
                        </li>
                    @empty
                        <li class="px-2 py-3 text-center small text-muted">{{ __('You are all caught up.') }}</li>
                    @endforelse
                </ul>
            </div>
            <form method="POST" action="{{ route('settings.theme') }}">
                @csrf
                <input type="hidden" name="theme" value="{{ auth()->user()->theme === 'dark' ? 'light' : 'dark' }}">
                <button class="btn btn-outline-light" type="submit" aria-label="{{ __('Switch to :theme theme', ['theme' => __(auth()->user()->theme === 'dark' ? 'light' : 'dark')]) }}">
                    <i class="bi {{ auth()->user()->theme === 'dark' ? 'bi-sun' : 'bi-moon-stars' }} me-sm-1"></i>
                    <span class="d-none d-sm-inline">{{ __('Switch to :theme theme', ['theme' => __(auth()->user()->theme === 'dark' ? 'light' : 'dark')]) }}</span>
                </button>
            </form>
            <form method="POST" action="{{ route('settings.language') }}">
                @csrf
                <input type="hidden" name="locale" value="{{ app()->getLocale() === 'km' ? 'en' : 'km' }}">
                <button class="btn btn-outline-light" type="submit" aria-label="{{ app()->getLocale() === 'km' ? __('Switch to English') : __('Switch to Khmer') }}">
                    <i class="bi bi-translate me-sm-1"></i>
                    <span class="d-none d-sm-inline">{{ app()->getLocale() === 'km' ? 'English' : 'ខ្មែរ' }}</span>
                </button>
            </form>
            <div class="dropdown">
            <button class="btn profile-button dropdown-toggle" data-bs-toggle="dropdown">
                <i class="bi bi-person-circle me-1"></i>{{ auth()->user()->name }}
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="{{ route('settings.edit') }}">{{ __('Profile') }}</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="dropdown-item">{{ __('Logout') }}</button>
                    </form>
                </li>
            </ul>
            </div>
        </div>
    </div>
</nav>
