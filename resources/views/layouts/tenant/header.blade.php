<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav menu-nav-section">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
        </li>
    </ul>
    
    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto icons-nav-section">
        <!-- Dashboard Switcher -->
        <li class="nav-item dropdown dashboard-switcher">
            <a class="nav-link dropdown-toggle" href="#" id="dashboardDropdown" role="button"
                data-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-tachometer-alt mr-1"></i>
                <span class="d-none d-md-inline" id="current-dashboard-name">@translate('dashboard')</span>
            </a>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dashboardDropdown" id="dashboard-dropdown-menu">
                <h6 class="dropdown-header">@translate('switch_dashboard')</h6>
                <div id="dashboards-list">
                    <a href="{{ route('home') }}" class="dropdown-item dashboard-switcher-item {{ request()->routeIs('home') ? 'active' : '' }}">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-home mr-2"></i>
                            <div>
                                <div class="font-weight-bold">@translate('main_dashboard')</div>
                                <small class="text-muted">@translate('overview_and_stats')</small>
                            </div>
                        </div>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="{{ route('tenant.dashboard.hr') }}" class="dropdown-item dashboard-switcher-item {{ request()->routeIs('tenant.dashboard.hr') ? 'active' : '' }}">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-users mr-2"></i>
                            <div>
                                <div class="font-weight-bold">@translate('hr_dashboard')</div>
                                <small class="text-muted">@translate('human_resources')</small>
                            </div>
                        </div>
                    </a>
                    <a href="{{ route('tenant.dashboard.crm') }}" class="dropdown-item dashboard-switcher-item {{ request()->routeIs('tenant.dashboard.crm') ? 'active' : '' }}">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-handshake mr-2"></i>
                            <div>
                                <div class="font-weight-bold">@translate('crm_dashboard')</div>
                                <small class="text-muted">@translate('customer_relationship')</small>
                            </div>
                        </div>
                    </a>
                    <a href="{{ route('tenant.dashboard.pos') }}" class="dropdown-item dashboard-switcher-item {{ request()->routeIs('tenant.dashboard.pos') ? 'active' : '' }}">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-cash-register mr-2"></i>
                            <div>
                                <div class="font-weight-bold">@translate('pos_dashboard')</div>
                                <small class="text-muted">@translate('point_of_sale')</small>
                            </div>
                        </div>
                    </a>
                    <a href="{{ route('tenant.dashboard.accounting') }}" class="dropdown-item dashboard-switcher-item {{ request()->routeIs('tenant.dashboard.accounting') ? 'active' : '' }}">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-calculator mr-2"></i>
                            <div>
                                <div class="font-weight-bold">@translate('accounting_dashboard')</div>
                                <small class="text-muted">@translate('financial_management')</small>
                            </div>
                        </div>
                    </a>
                    <a href="{{ route('tenant.dashboard.sales') }}" class="dropdown-item dashboard-switcher-item {{ request()->routeIs('tenant.dashboard.sales') ? 'active' : '' }}">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-chart-line mr-2"></i>
                            <div>
                                <div class="font-weight-bold">@translate('sales_dashboard')</div>
                                <small class="text-muted">@translate('sales_analytics')</small>
                            </div>
                        </div>
                    </a>
                    <a href="{{ route('tenant.dashboard.inventory') }}" class="dropdown-item dashboard-switcher-item {{ request()->routeIs('tenant.dashboard.inventory') ? 'active' : '' }}">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-boxes mr-2"></i>
                            <div>
                                <div class="font-weight-bold">@translate('inventory_dashboard')</div>
                                <small class="text-muted">@translate('stock_management')</small>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </li>

        <!-- Brand Switcher -->
        <li class="nav-item dropdown brand-switcher">
            <a class="nav-link dropdown-toggle" href="#" id="brandDropdown" role="button"
                data-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-building mr-1"></i>
                <span class="d-none d-md-inline" id="current-brand-name">@translate('switch_brand')</span>
            </a>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="brandDropdown" id="brand-dropdown-menu">
                <h6 class="dropdown-header">@translate('select_brand')</h6>
                <div id="brands-list">
                    <div class="text-center py-3">
                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                            <span class="sr-only">@translate('loading')</span>
                        </div>
                    </div>
                </div>
            </div>
        </li>

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

<script>
$(document).ready(function() {
    // Initialize dashboard switcher
    initializeDashboardSwitcher();
    
    // Load brands for switcher
    loadBrandsForSwitcher();
    
    // Dashboard switcher functionality
    function initializeDashboardSwitcher() {
        // Update current dashboard name based on current route
        const currentRoute = '{{ request()->route()->getName() }}';
        let dashboardName = '@translate("dashboard")';
        
        switch(currentRoute) {
            case 'home':
                dashboardName = '@translate("main_dashboard")';
                break;
            case 'tenant.dashboard.hr':
                dashboardName = '@translate("hr_dashboard")';
                break;
            case 'tenant.dashboard.crm':
                dashboardName = '@translate("crm_dashboard")';
                break;
            case 'tenant.dashboard.pos':
                dashboardName = '@translate("pos_dashboard")';
                break;
            case 'tenant.dashboard.accounting':
                dashboardName = '@translate("accounting_dashboard")';
                break;
            case 'tenant.dashboard.sales':
                dashboardName = '@translate("sales_dashboard")';
                break;
            case 'tenant.dashboard.inventory':
                dashboardName = '@translate("inventory_dashboard")';
                break;
        }
        
        $('#current-dashboard-name').text(dashboardName);
        
        // Add click handlers for dashboard switcher items
        $('.dashboard-switcher-item').on('click', function(e) {
            const href = $(this).attr('href');
            if (href && href !== '#') {
                window.location.href = href;
            }
        });
    }
    
    // Brand switcher functionality
    function loadBrandsForSwitcher() {
        $.ajax({
            url: '{{ route("tenant.brands.dashboard-data") }}',
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    displayBrandsInSwitcher(response.data);
                }
            },
            error: function(xhr) {
                console.error('Error loading brands for switcher:', xhr);
                $('#brands-list').html('<div class="text-center py-3 text-muted">@translate("error_loading_brands")</div>');
            }
        });
    }
    
    function displayBrandsInSwitcher(brands) {
        if (brands.length === 0) {
            $('#brands-list').html('<div class="text-center py-3 text-muted">@translate("no_brands_found")</div>');
            return;
        }
        
        let html = '';
        brands.forEach(function(brand) {
            html += `
                <a href="#" class="dropdown-item brand-switcher-item" data-brand-id="${brand.id}" data-brand-name="${brand.name}">
                    <div class="d-flex align-items-center">
                        <img src="${brand.logo_url}" alt="${brand.name}" class="brand-switcher-logo mr-2">
                        <div>
                            <div class="font-weight-bold">${brand.name}</div>
                            <small class="text-muted">${brand.modules_count} @translate('modules')</small>
                        </div>
                    </div>
                </a>
            `;
        });
        
        $('#brands-list').html(html);
        
        // Add click handlers
        $('.brand-switcher-item').on('click', function(e) {
            e.preventDefault();
            const brandId = $(this).data('brand-id');
            const brandName = $(this).data('brand-name');
            
            // Update current brand display
            $('#current-brand-name').text(brandName);
            
            // Show brand modules
            showBrandModulesFromSwitcher(brandId);
        });
    }
    
    function showBrandModulesFromSwitcher(brandId) {
        $.ajax({
            url: `{{ url('tenant/brands') }}/${brandId}/modules`,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    displayBrandModulesFromSwitcher(response.data);
                }
            },
            error: function(xhr) {
                console.error('Error loading brand modules:', xhr);
                Swal.fire({
                    title: '@translate("error")',
                    text: '@translate("error_loading_modules")',
                    icon: 'error'
                });
            }
        });
    }
    
    function displayBrandModulesFromSwitcher(modules) {
        let html = '<div class="row">';
        
        if (modules.length === 0) {
            html = '<div class="text-center py-4 text-muted">@translate("no_modules_assigned")</div>';
        } else {
            modules.forEach(function(module) {
                html += `
                    <div class="col-md-6 col-sm-12 mb-3">
                        <div class="module-card" onclick="navigateToModule('${module.route}')">
                            <div class="module-card__icon">
                                <i class="${module.icon || 'fas fa-cube'}"></i>
                            </div>
                            <div class="module-card__content">
                                <h6 class="module-name">${module.name}</h6>
                                <p class="module-description">${module.description || ''}</p>
                            </div>
                        </div>
                    </div>
                `;
            });
        }
        
        html += '</div>';
        
        Swal.fire({
            title: '@translate("brand_modules")',
            html: html,
            width: '600px',
            showCloseButton: true,
            showConfirmButton: false,
            customClass: {
                popup: 'brand-modules-modal'
            }
        });
    }
});
</script>

