<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('home') }}" class="brand-link">
        <img src="{{ asset('assets/shared/images/icons/logo/favicon.svg') }}" alt="logo"
            class="brand-image img-circle elevation-3 float-revert">
        <span class="brand-text font-weight-light">{{ $tenant->name ?? env('APP_NAME') }}</span>
    </a>
    
    <!-- Sidebar -->
    <div class="sidebar">
        <!-- User Panel -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{ auth()->user()->avatar }}" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="{{ route('tenant.profile.index') }}" class="d-block">{{ auth()->user()->name }}</a>
            </div>
        </div>

        <!-- Sidebar Search Form -->
        <div class="form-inline">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="@translate('search')"
                    aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
            <div class="sidebar-search-results">
                <div class="list-group">
                    <a href="#" class="list-group-item">
                        <div class="search-title">
                            <div class="text-light">
                                @translate('no_elements_found')
                            </div>
                        </div>
                        <div class="search-path"></div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                
                <!-- Dashboard -->
                <li class="nav-header">@translate('main')</li>
                <li class="nav-item">
                    <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>@translate('dashboard')</p>
                    </a>
                </li>

                <!-- Access Control -->
                <li class="nav-header">@translate('access_control')</li>
                
                <!-- Roles -->
                <li class="nav-item">
                    <a href="{{ route('tenant.roles.index') }}" class="nav-link {{ request()->routeIs('tenant.roles.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user-shield"></i>
                        <p>@translate('roles')</p>
                    </a>
                </li>

                <!-- Permissions -->
                <li class="nav-item">
                    <a href="{{ route('tenant.permissions.index') }}" class="nav-link {{ request()->routeIs('tenant.permissions.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-key"></i>
                        <p>@translate('permissions')</p>
                    </a>
                </li>

                <!-- Users -->
                <li class="nav-item">
                    <a href="{{ route('tenant.users.index') }}" class="nav-link {{ request()->routeIs('tenant.users.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-users-cog"></i>
                        <p>@translate('users')</p>
                    </a>
                </li>

                <!-- Customer Management -->
                <li class="nav-header">@translate('customer_management')</li>
                
                <!-- Brands -->
                <li class="nav-item">
                    <a href="{{ route('tenant.brands.index') }}" class="nav-link {{ request()->routeIs('tenant.brands.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tags"></i>
                        <p>@translate('brands')</p>
                    </a>
                </li>
                
                <!-- Branches -->
                <li class="nav-item">
                    <a href="{{ route('tenant.branches.index') }}" class="nav-link {{ request()->routeIs('tenant.branches.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-building"></i>
                        <p>@translate('branches')</p>
                    </a>
                </li>

                <!-- Support -->
                <li class="nav-header">@translate('support')</li>
                
                <!-- Tickets -->
                <li class="nav-item">
                    <a href="{{ route('tenant.tickets.index') }}" class="nav-link {{ request()->routeIs('tenant.tickets.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-ticket-alt"></i>
                        <p>@translate('tickets')</p>
                    </a>
                </li>

                <!-- Account -->
                <li class="nav-header">@translate('account')</li>
                
                <!-- Profile -->
                <li class="nav-item">
                    <a href="{{ route('tenant.profile.index') }}" class="nav-link {{ request()->routeIs('tenant.profile.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user"></i>
                        <p>@translate('my_profile')</p>
                    </a>
                </li>

                <!-- Settings -->
                <li class="nav-item">
                    <a href="{{ route('tenant.settings.index') }}" class="nav-link {{ request()->routeIs('tenant.settings.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-cog"></i>
                        <p>@translate('settings')</p>
                    </a>
                </li>

                <!-- Security & Monitoring -->
                <li class="nav-header">@translate('security')</li>
                
                <!-- Login Attempts -->
                <li class="nav-item">
                    <a href="{{ route('tenant.login-attempts.index') }}" class="nav-link {{ request()->routeIs('tenant.login-attempts.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-shield-alt"></i>
                        <p>@translate('login_attempts')</p>
                    </a>
                </li>

                <!-- Activity Logs -->
                <li class="nav-item">
                    <a href="{{ route('tenant.activity-logs.index') }}" class="nav-link {{ request()->routeIs('tenant.activity-logs.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-history"></i>
                        <p>@translate('activity_logs')</p>
                    </a>
                </li>

            </ul>
        </nav>
    </div>
</aside>

