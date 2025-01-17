<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav menu-nav-section">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
        </li>
    </ul>
    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto icons-nav-section">
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="notificationsDropdown"
                data-notifications-list-route="{{ route('notifications.list') }}"
                data-notifications-route-route="{{ route('notifications.index') }}"
                data-no-notifications-message="{{ translate('no_notifications_yet') }}"
                data-notifications-view-all-message="{{ translate('view_all') }}"
                role="button" data-toggle="dropdown"
                aria-expanded="false">
                <i class="fas fa-bell"></i>
                @if ($notificationsCount > 0)
                    <span class="badge badge-danger">{{ $notificationsCount }}</span>
                @endif
            </a>
            <div class="dropdown-menu notifications-menu dropdown-menu-right" aria-labelledby="notificationsDropdown">
                <h6 class="dropdown-header">@translate('notifications')</h6>
                <div class="notifications-list"></div>
            </div>
        </li>

        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown"
                aria-expanded="false">
                <i class="fas fa-desktop"></i>
            </a>
            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                <li>
                    <a class="dropdown-item" href="{{ route('home') }}">
                        <i class="fas fa-home"></i> Home
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="#" onclick="window.history.back()">
                        <i class="fas fa-arrow-left"></i> Go Back
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" id="FullScreen" href="#">
                        <i class="fas fa-expand"></i> Full Screen
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="#" onclick="window.location.reload();">
                        <i class="fas fa-sync-alt"></i> Reload
                    </a>
                </li>
            </ul>
        </li>
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="fas fa-user-circle"></i> {{ auth()->user()->name }}
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <div class="dropdown-divider"></div>

                <a class="dropdown-item" href="{{ route('landlord.profile.index') }}">
                    <i class="fas fa-user"></i> @translate('my_account')
                </a>

                <div class="logout-container">
                    <a style="cursor: pointer" data-form="logout-form" class="logout-btn dropdown-item">
                        <i class="fas fa-sign-out-alt"></i> @translate('logout')
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
                @if (Auth::user()->hasRole(['super_admin', 'admin']))
                    <a style="cursor: pointer" data-form="logout-all-form" class="logout-btn dropdown-item">
                        <i class="fas fa-sign-out-alt"></i> @translate('logout_all')
                    </a>
                    <form id="logout-all-form" action="{{ route('logout-all') }}" method="POST"
                        style="display: none;">
                        @csrf
                    </form>
                @endif

            </div>
        </li>
    </ul>
</nav>
