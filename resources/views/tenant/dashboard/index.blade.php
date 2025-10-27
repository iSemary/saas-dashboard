@extends('layouts.tenant.app')

@section('title', translate('dashboard'))

@section('content')
<div class="container-fluid">
    <!-- Welcome Section -->
    <div class="row mb-4 animate-on-scroll">
        <div class="col-12">
            <div class="modern-card">
                <div class="modern-card__header">
                    <h2 class="heading-2 mb-0">@translate('welcome_back'), {{ $user->name }}!</h2>
                    <div class="card-actions">
                        <button class="modern-btn modern-btn--ghost modern-btn--sm" data-tooltip="@translate('refresh_dashboard')">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>
                <div class="modern-card__body">
                    <p class="body-large text-muted mb-0">@translate('heres_whats_happening_with_your_business_today')</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid animate-on-scroll">
        <div class="stat-widget stat-widget--primary">
            <div class="stat-widget__header">
                <h3 class="stat-title">@translate('total_users')</h3>
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            <div class="stat-widget__content">
                <h3 class="stat-number" id="total-users">{{ $stats['overview']['total_users'] ?? 0 }}</h3>
                <div class="stat-change stat-change--positive">
                    <i class="fas fa-arrow-up change-icon"></i>
                    <span>+12%</span>
                </div>
            </div>
            <div class="stat-widget__footer">
                <span class="stat-label">@translate('vs_last_month')</span>
                <a href="{{ route('tenant.users.index') }}" class="stat-link">@translate('view_details')</a>
            </div>
        </div>

        <div class="stat-widget stat-widget--success">
            <div class="stat-widget__header">
                <h3 class="stat-title">@translate('active_users')</h3>
                <div class="stat-icon">
                    <i class="fas fa-user-check"></i>
                </div>
            </div>
            <div class="stat-widget__content">
                <h3 class="stat-number" id="active-users">{{ $stats['overview']['active_users'] ?? 0 }}</h3>
                <div class="stat-change stat-change--positive">
                    <i class="fas fa-arrow-up change-icon"></i>
                    <span>+8%</span>
                </div>
            </div>
            <div class="stat-widget__footer">
                <span class="stat-label">@translate('vs_last_month')</span>
                <a href="{{ route('tenant.users.index') }}" class="stat-link">@translate('view_details')</a>
            </div>
        </div>

        <div class="stat-widget stat-widget--warning">
            <div class="stat-widget__header">
                <h3 class="stat-title">@translate('projects')</h3>
                <div class="stat-icon">
                    <i class="fas fa-folder"></i>
                </div>
            </div>
            <div class="stat-widget__content">
                <h3 class="stat-number" id="total-projects">{{ $stats['overview']['total_projects'] ?? 0 }}</h3>
                <div class="stat-change stat-change--neutral">
                    <i class="fas fa-minus change-icon"></i>
                    <span>0%</span>
                </div>
            </div>
            <div class="stat-widget__footer">
                <span class="stat-label">@translate('vs_last_month')</span>
                <a href="#" class="stat-link">@translate('view_details')</a>
            </div>
        </div>

        <div class="stat-widget stat-widget--accent">
            <div class="stat-widget__header">
                <h3 class="stat-title">@translate('growth_rate')</h3>
                <div class="stat-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
            <div class="stat-widget__content">
                <h3 class="stat-number" id="growth-rate">{{ $stats['overview']['growth_rate'] ?? 0 }}%</h3>
                <div class="stat-change stat-change--positive">
                    <i class="fas fa-arrow-up change-icon"></i>
                    <span>+5%</span>
                </div>
            </div>
            <div class="stat-widget__footer">
                <span class="stat-label">@translate('vs_last_month')</span>
                <a href="#" class="stat-link">@translate('view_details')</a>
            </div>
        </div>
    </div>

    <!-- Brands Section -->
    <div class="row animate-on-scroll">
        <div class="col-md-12">
            <div class="modern-card">
                <div class="modern-card__header">
                    <h3 class="heading-4">@translate('brands')</h3>
                    <div class="card-actions">
                        <button class="modern-btn modern-btn--primary modern-btn--sm open-create-modal" 
                                data-modal-link="{{ route('tenant.brands.create') }}" 
                                data-modal-title="@translate('add_brand')">
                            <i class="fas fa-plus"></i>
                            @translate('add_brand')
                        </button>
                    </div>
                </div>
                <div class="modern-card__body">
                    <div class="brands-grid" id="brands-grid">
                        <!-- Brands will be loaded via AJAX -->
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">@translate('loading')</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row animate-on-scroll">
        <div class="col-md-6">
            <div class="chart-card">
                <div class="chart-card__header">
                    <h3 class="heading-4">@translate('revenue_trend')</h3>
                    <div class="chart-controls">
                        <button class="modern-btn modern-btn--ghost modern-btn--sm" data-tooltip="@translate('export_chart')">
                            <i class="fas fa-download"></i>
                        </button>
                    </div>
                </div>
                <div class="chart-card__content">
                    <canvas id="revenueChart" height="200"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="chart-card">
                <div class="chart-card__header">
                    <h3 class="heading-4">@translate('user_growth')</h3>
                    <div class="chart-controls">
                        <button class="modern-btn modern-btn--ghost modern-btn--sm" data-tooltip="@translate('export_chart')">
                            <i class="fas fa-download"></i>
                        </button>
                    </div>
                </div>
                <div class="chart-card__content">
                    <canvas id="userGrowthChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row animate-on-scroll">
        <div class="col-md-12">
            <div class="modern-card">
                <div class="modern-card__header">
                    <h3 class="heading-4">@translate('quick_actions')</h3>
                </div>
                <div class="modern-card__body">
                    <div class="row">
                        <div class="col-md-3 col-sm-6 mb-3">
                            <a href="{{ route('tenant.users.create') }}" class="btn-app">
                                <i class="fas fa-user-plus"></i>
                                <span>@translate('add_user')</span>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <a href="{{ route('tenant.roles.create') }}" class="btn-app">
                                <i class="fas fa-user-shield"></i>
                                <span>@translate('add_role')</span>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <a href="{{ route('tenant.permissions.create') }}" class="btn-app">
                                <i class="fas fa-key"></i>
                                <span>@translate('add_permission')</span>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <a href="{{ route('tenant.settings.index') }}" class="btn-app">
                                <i class="fas fa-cog"></i>
                                <span>@translate('settings')</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row animate-on-scroll">
        <div class="col-md-8">
            <div class="modern-card">
                <div class="modern-card__header">
                    <h3 class="heading-4">@translate('recent_activity')</h3>
                    <div class="card-actions">
                        <a href="#" class="modern-btn modern-btn--ghost modern-btn--sm">@translate('view_all')</a>
                    </div>
                </div>
                <div class="modern-card__body">
                    <div class="modern-table">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>@translate('user')</th>
                                    <th>@translate('action')</th>
                                    <th>@translate('time')</th>
                                    <th>@translate('status')</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="user-avatar mr-2">JD</div>
                                            <span>John Doe</span>
                                        </div>
                                    </td>
                                    <td>@translate('created_new_user')</td>
                                    <td>2 minutes ago</td>
                                    <td><span class="status-badge status-badge--success">@translate('completed')</span></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="user-avatar mr-2">JS</div>
                                            <span>Jane Smith</span>
                                        </div>
                                    </td>
                                    <td>@translate('updated_profile')</td>
                                    <td>15 minutes ago</td>
                                    <td><span class="status-badge status-badge--success">@translate('completed')</span></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="user-avatar mr-2">MJ</div>
                                            <span>Mike Johnson</span>
                                        </div>
                                    </td>
                                    <td>@translate('changed_password')</td>
                                    <td>1 hour ago</td>
                                    <td><span class="status-badge status-badge--success">@translate('completed')</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="modern-card">
                <div class="modern-card__header">
                    <h3 class="heading-4">@translate('system_status')</h3>
                </div>
                <div class="modern-card__body">
                    <div class="info-card">
                        <div class="info-card__icon">
                            <i class="fas fa-server"></i>
                        </div>
                        <div class="info-card__content">
                            <h4>@translate('server_status')</h4>
                            <p>@translate('all_systems_operational')</p>
                        </div>
                        <div class="info-card__action">
                            <span class="status-badge status-badge--success">@translate('online')</span>
                        </div>
                    </div>

                    <div class="info-card">
                        <div class="info-card__icon">
                            <i class="fas fa-database"></i>
                        </div>
                        <div class="info-card__content">
                            <h4>@translate('database_status')</h4>
                            <p>@translate('connection_stable')</p>
                        </div>
                        <div class="info-card__action">
                            <span class="status-badge status-badge--success">@translate('connected')</span>
                        </div>
                    </div>

                    <div class="info-card">
                        <div class="info-card__icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div class="info-card__content">
                            <h4>@translate('security_status')</h4>
                            <p>@translate('no_threats_detected')</p>
                        </div>
                        <div class="info-card__action">
                            <span class="status-badge status-badge--success">@translate('secure')</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Initialize modern dashboard
    if (window.modernDashboard) {
        window.modernDashboard.animateStats();
    }

    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart');
    if (revenueCtx) {
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: '@translate("revenue")',
                    data: [12, 19, 3, 5, 2, 3],
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#3b82f6',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#6b7280'
                        }
                    },
                    y: {
                        grid: {
                            color: '#f3f4f6'
                        },
                        ticks: {
                            color: '#6b7280'
                        }
                    }
                },
                elements: {
                    line: {
                        borderWidth: 3
                    }
                }
            }
        });
    }

    // User Growth Chart
    const userGrowthCtx = document.getElementById('userGrowthChart');
    if (userGrowthCtx) {
        new Chart(userGrowthCtx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: '@translate("new_users")',
                    data: [5, 10, 8, 15, 12, 20],
                    backgroundColor: 'rgba(34, 197, 94, 0.8)',
                    borderColor: '#22c55e',
                    borderWidth: 0,
                    borderRadius: 6,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#6b7280'
                        }
                    },
                    y: {
                        grid: {
                            color: '#f3f4f6'
                        },
                        ticks: {
                            color: '#6b7280'
                        },
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // Add refresh functionality
    $('[data-tooltip="@translate('refresh_dashboard')"]').on('click', function() {
        const $btn = $(this);
        const $icon = $btn.find('i');
        
        // Add loading state
        $btn.addClass('modern-btn--loading');
        $icon.addClass('fa-spin');
        
        // Simulate refresh (replace with actual AJAX call)
        setTimeout(() => {
            $btn.removeClass('modern-btn--loading');
            $icon.removeClass('fa-spin');
            
            // Show success notification
            if (window.modernDashboard) {
                window.modernDashboard.showNotification('@translate("dashboard_refreshed")', 'success');
            }
        }, 1500);
    });

    // Add export functionality for charts
    $('[data-tooltip="@translate('export_chart')"]').on('click', function() {
        const $btn = $(this);
        const $card = $btn.closest('.chart-card');
        const canvas = $card.find('canvas')[0];
        
        if (canvas) {
            const link = document.createElement('a');
            link.download = 'chart-export.png';
            link.href = canvas.toDataURL();
            link.click();
            
            if (window.modernDashboard) {
                window.modernDashboard.showNotification('@translate("chart_exported")', 'success');
            }
        }
        });
    });

    // Load brands for dashboard
    function loadBrands() 
    {
        $.ajax({
            url: '{{ route("tenant.brands.dashboard-data") }}',
            type: 'GET',
            success: function(response) 
            {
                if (response.success) 
                {
                    displayBrands(response.data);
                }
            },
            error: function(xhr) 
            {
                console.error('Error loading brands:', xhr);
                $('#brands-grid').html('<div class="text-center py-4 text-muted">@translate("error_loading_brands")</div>');
            }
        });
    }

    // Display brands in grid
    function displayBrands(brands) 
    {
        if (brands.length === 0) 
        {
            $('#brands-grid').html('<div class="text-center py-4 text-muted">@translate("no_brands_found")</div>');
            return;
        }

        let html = '<div class="row">';
        
        brands.forEach(function(brand) 
        {
            html += `
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="brand-card" data-brand-id="${brand.id}">
                        <div class="brand-card__logo">
                            <img src="${brand.logo_url}" alt="${brand.name}" class="brand-logo">
                        </div>
                        <div class="brand-card__content">
                            <h4 class="brand-name">${brand.name}</h4>
                            <p class="brand-modules-count">${brand.modules_count} @translate('modules')</p>
                            <div class="brand-status">
                                <span class="badge badge-${brand.status === 'active' ? 'success' : 'warning'}">${brand.status}</span>
                            </div>
                        </div>
                        <div class="brand-card__actions">
                            <button class="btn btn-sm btn-primary" onclick="showBrandModules(${brand.id})">
                                <i class="fas fa-eye"></i> @translate('view_modules')
                            </button>
                        </div>
                    </div>
                </div>
            `;
        });
        
        html += '</div>';
        $('#brands-grid').html(html);
    }

    // Show brand modules modal
    function showBrandModules(brandId) 
    {
        $.ajax({
            url: `{{ url('tenant/brands') }}/${brandId}/modules`,
            type: 'GET',
            success: function(response) 
            {
                if (response.success) 
                {
                    displayBrandModulesModal(response.data, brandId);
                }
            },
            error: function(xhr) 
            {
                console.error('Error loading brand modules:', xhr);
                Swal.fire({
                    title: '@translate("error")',
                    text: '@translate("error_loading_modules")',
                    icon: 'error'
                });
            }
        });
    }

    // Display brand modules in modal
    function displayBrandModulesModal(modules, brandId) 
    {
        let html = '<div class="row">';
        
        if (modules.length === 0) 
        {
            html = '<div class="text-center py-4 text-muted">@translate("no_modules_assigned")</div>';
        } 
        else 
        {
            modules.forEach(function(module) 
            {
                html += `
                    <div class="col-md-4 col-sm-6 mb-3">
                        <div class="module-card" onclick="navigateToModule('${module.route}')">
                            <div class="module-card__icon">
                                <i class="${module.icon || 'fas fa-cube'}"></i>
                            </div>
                            <div class="module-card__content">
                                <h5 class="module-name">${module.name}</h5>
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
            width: '800px',
            showCloseButton: true,
            showConfirmButton: false,
            customClass: {
                popup: 'brand-modules-modal'
            }
        });
    }

    // Navigate to module dashboard
    function navigateToModule(route) 
    {
        if (route) 
        {
            window.location.href = route;
        } 
        else 
        {
            Swal.fire({
                title: '@translate("coming_soon")',
                text: '@translate("module_coming_soon")',
                icon: 'info'
            });
        }
    }

    // Load brands on page load
    loadBrands();
});
</script>
@endsection

