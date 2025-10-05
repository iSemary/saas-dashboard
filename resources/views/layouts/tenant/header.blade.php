<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav menu-nav-section">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
        </li>
    </ul>
    
    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto icons-nav-section">
        <!-- Notifications -->
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="notificationsDropdown" role="button"
                data-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-bell"></i>
                @if (isset($notificationsCount) && $notificationsCount > 0)
                    <span class="badge badge-danger">{{ $notificationsCount }}</span>
                @endif
            </a>
            <div class="dropdown-menu notifications-menu dropdown-menu-right" aria-labelledby="notificationsDropdown">
                <h6 class="dropdown-header">@translate('notifications')</h6>
                <div class="notifications-list">
                    <p class="text-center text-muted py-3">@translate('no_new_notifications')</p>
                </div>
            </div>
        </li>

        <!-- User Profile Dropdown -->
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                data-toggle="dropdown" aria-expanded="false">
                <img src="{{ auth()->user()->avatar }}" class="img-circle elevation-2" alt="User Image"
                    style="width: 30px; height: 30px;">
                <span class="ml-2 d-none d-md-inline">{{ auth()->user()->name }}</span>
            </a>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                <div class="dropdown-header">
                    <strong>{{ auth()->user()->name }}</strong><br>
                    <small class="text-muted">{{ auth()->user()->email }}</small>
                </div>
                <div class="dropdown-divider"></div>
                <a href="{{ route('tenant.profile.index') }}" class="dropdown-item">
                    <i class="fas fa-user mr-2"></i> @translate('my_profile')
                </a>
                <a href="{{ route('tenant.settings.index') }}" class="dropdown-item">
                    <i class="fas fa-cog mr-2"></i> @translate('settings')
                </a>
                <div class="dropdown-divider"></div>
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="dropdown-item text-danger">
                        <i class="fas fa-sign-out-alt mr-2"></i> @translate('logout')
                    </button>
                </form>
            </div>
        </li>
    </ul>
</nav>

