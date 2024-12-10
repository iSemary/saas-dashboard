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
            <a class="nav-link" href="#">
                <i class="fas fa-arrow-left" onclick="window.history.back()"></i>
            </a>
        </li>
        @if (Auth::user()->hasRole(['super_admin', 'admin']))
            <li class="nav-item dropdown">
                <a class="nav-link" href="{{ route('admin.index') }}">
                    <i class="fas fa-tachometer-alt"></i>
                </a>
            </li>
        @endif
        <li class="nav-item dropdown ">
            <a class="nav-link" id="FullScreen" href="#">
                <i class="fas fa-expand"></i>
            </a>
        </li>
        <li class="nav-item dropdown">
            <a class="nav-link" href="#">
                <i class="fas fa-sync-alt" onClick="window.location.reload();"></i>
            </a>
        </li>
        @if (Auth::user()->hasRole(['super_admin', 'admin']))
            <li class="nav-item dropdown">
                <a class="nav-link" href="{{ route('admin.users-activity') }}">
                    <i class="fas fa-user-lock"></i>
                </a>
            </li>
        @endif
        <!-- Settings Dropdown Menu -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="fas fa-cog"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                @if (Auth::user()->hasRole(['super_admin']))
                    <a class="dropdown-item" href="{{ route('admin.settings.edit', \Auth::id()) }}">
                        <i class="fas fa-sliders-h"></i> @lang('settings')
                    </a>
                @endif
                <div class="dropdown-divider"></div>
                <a style="cursor: pointer" data-form="logout-form" class="logout-btn dropdown-item">
                    <i class="fas fa-sign-out-alt"></i> @lang('logout')
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
                @if (Auth::user()->hasRole(['super_admin', 'admin']))
                    <a style="cursor: pointer" data-form="logout-all-form" class="logout-btn dropdown-item">
                        <i class="fas fa-sign-out-alt"></i> @lang('logout_all')
                    </a>
                    <form id="logout-all-form" action="{{ route('logout-all') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                @endif

            </div>
        </li>
    </ul>
</nav>
