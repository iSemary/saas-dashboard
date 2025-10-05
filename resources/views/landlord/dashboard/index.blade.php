@extends('layouts.landlord.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">
                    <i class="mdi mdi-view-dashboard me-2"></i>
                    Dashboard
                </h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row" id="stats-cards">
        <!-- Loading placeholder -->
        <div class="col-12">
            <div class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Loading dashboard statistics...</p>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <!-- User Growth Chart -->
        <div class="col-xl-6 col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="mdi mdi-account-group me-2"></i>
                        User Growth (Last 30 Days)
                    </h4>
                </div>
                <div class="card-body">
                    <canvas id="userChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Tenant Growth Chart -->
        <div class="col-xl-6 col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="mdi mdi-domain me-2"></i>
                        Tenant Growth (Last 30 Days)
                    </h4>
                </div>
                <div class="card-body">
                    <canvas id="tenantChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Email Activity Chart -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="mdi mdi-email me-2"></i>
                        Email Activity (Last 30 Days)
                    </h4>
                </div>
                <div class="card-body">
                    <canvas id="emailChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Module Statistics -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="mdi mdi-puzzle me-2"></i>
                        Module Statistics
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row" id="module-stats">
                        <!-- Loading placeholder -->
                        <div class="col-12">
                            <div class="text-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2">Loading module statistics...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .stat-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 15px;
        color: white;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }

    .stat-card.clickable-card {
        cursor: pointer;
    }

    .stat-card.clickable-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.2);
    }

    .stat-card.users {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .stat-card.tenants {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }

    .stat-card.categories {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }

    .stat-card.types {
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    }

    .stat-card.industries {
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
    }

    .stat-card.email-templates {
        background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
    }

    .stat-card.brands {
        background: linear-gradient(135deg, #fd79a8 0%, #fdcb6e 100%);
    }

    .stat-card.brand-modules {
        background: linear-gradient(135deg, #6c5ce7 0%, #a29bfe 100%);
    }

    .stat-card.languages {
        background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%);
    }

    .stat-card.system {
        background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
    }

    .stat-icon {
        font-size: 2.5rem;
        opacity: 0.8;
    }

    .stat-number {
        font-size: 2rem;
        font-weight: 700;
        margin: 0;
    }

    .stat-label {
        font-size: 0.9rem;
        opacity: 0.9;
        margin: 0;
    }

    .stat-change {
        font-size: 0.8rem;
        margin-top: 0.5rem;
    }

    .stat-change.positive {
        color: #4ade80;
    }

    .stat-change.negative {
        color: #f87171;
    }

    .module-stat-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 1.5rem;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
    }

    .module-stat-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }

    .module-name {
        font-size: 1.1rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 1rem;
    }

    .module-stat-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.5rem 0;
        border-bottom: 1px solid #f3f4f6;
    }

    .module-stat-item:last-child {
        border-bottom: none;
    }

    .module-stat-label {
        color: #6b7280;
        font-size: 0.9rem;
    }

    .module-stat-value {
        font-weight: 600;
        color: #374151;
    }

    .card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
    }

    .card:hover {
        box-shadow: 0 4px 20px rgba(0,0,0,0.12);
    }

    .card-header {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        border-bottom: 1px solid #e2e8f0;
        border-radius: 15px 15px 0 0 !important;
    }

    .card-title {
        color: #374151;
        font-weight: 600;
        margin: 0;
    }

    .page-title {
        color: #374151;
        font-weight: 700;
    }

    .breadcrumb {
        background: transparent;
        padding: 0;
        margin: 0;
    }

    .breadcrumb-item a {
        color: #6b7280;
        text-decoration: none;
    }

    .breadcrumb-item.active {
        color: #374151;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // Load dashboard data
    loadDashboardStats();
    loadUserChart();
    loadTenantChart();
    loadEmailChart();
    loadModuleStats();

    // Refresh data every 5 minutes
    setInterval(function() {
        loadDashboardStats();
    }, 300000);
});

function loadDashboardStats() {
    $.ajax({
        url: '{{ route("landlord.dashboard.stats") }}',
        type: 'GET',
        success: function(response) {
            if (response.success) {
                renderStatsCards(response.data);
            }
        },
        error: function(xhr) {
            console.error('Error loading dashboard stats:', xhr);
            showError('Failed to load dashboard statistics');
        }
    });
}

function renderStatsCards(stats) {
    const cardsHtml = `
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card stat-card users">
                <div class="card-body text-center">
                    <i class="mdi mdi-account-group stat-icon"></i>
                    <h3 class="stat-number">${stats.users.total}</h3>
                    <p class="stat-label">Total Users</p>
                    <div class="stat-change ${stats.users.growth_rate >= 0 ? 'positive' : 'negative'}">
                        <i class="mdi mdi-${stats.users.growth_rate >= 0 ? 'trending-up' : 'trending-down'}"></i>
                        ${Math.abs(stats.users.growth_rate)}% this month
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card stat-card tenants">
                <div class="card-body text-center">
                    <i class="mdi mdi-domain stat-icon"></i>
                    <h3 class="stat-number">${stats.tenants.total}</h3>
                    <p class="stat-label">Total Tenants</p>
                    <div class="stat-change ${stats.tenants.growth_rate >= 0 ? 'positive' : 'negative'}">
                        <i class="mdi mdi-${stats.tenants.growth_rate >= 0 ? 'trending-up' : 'trending-down'}"></i>
                        ${Math.abs(stats.tenants.growth_rate)}% this month
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card stat-card categories">
                <div class="card-body text-center">
                    <i class="mdi mdi-tag-multiple stat-icon"></i>
                    <h3 class="stat-number">${stats.categories.total}</h3>
                    <p class="stat-label">Categories</p>
                    <div class="stat-change positive">
                        <i class="mdi mdi-check-circle"></i>
                        ${stats.categories.active} active
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card stat-card brands clickable-card" data-module="brands">
                <div class="card-body text-center">
                    <i class="mdi mdi-store stat-icon"></i>
                    <h3 class="stat-number">${stats.brands.total}</h3>
                    <p class="stat-label">{{ translate('brands') }}</p>
                    <div class="stat-change ${stats.brands.growth_rate >= 0 ? 'positive' : 'negative'}">
                        <i class="mdi mdi-${stats.brands.growth_rate >= 0 ? 'trending-up' : 'trending-down'}"></i>
                        ${Math.abs(stats.brands.growth_rate)}% {{ translate('this_month') }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card stat-card brand-modules">
                <div class="card-body text-center">
                    <i class="mdi mdi-puzzle stat-icon"></i>
                    <h3 class="stat-number">${stats.brand_modules.active_subscriptions}</h3>
                    <p class="stat-label">{{ translate('active_module_subscriptions') }}</p>
                    <div class="stat-change positive">
                        <i class="mdi mdi-check-circle"></i>
                        ${stats.brand_modules.brands_with_modules} {{ translate('brands_with_modules') }}
                    </div>
                </div>
            </div>
        </div>
    `;
    
    $('#stats-cards').html(cardsHtml);
}

function loadUserChart() {
    $.ajax({
        url: '{{ route("landlord.dashboard.user-chart") }}',
        type: 'GET',
        success: function(response) {
            if (response.success) {
                renderUserChart(response.data);
            }
        },
        error: function(xhr) {
            console.error('Error loading user chart:', xhr);
        }
    });
}

function renderUserChart(data) {
    const ctx = document.getElementById('userChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.map(item => new Date(item.date).toLocaleDateString()),
            datasets: [{
                label: 'New Users',
                data: data.map(item => item.count),
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
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
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0,0,0,0.1)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
}

function loadTenantChart() {
    $.ajax({
        url: '{{ route("landlord.dashboard.tenant-chart") }}',
        type: 'GET',
        success: function(response) {
            if (response.success) {
                renderTenantChart(response.data);
            }
        },
        error: function(xhr) {
            console.error('Error loading tenant chart:', xhr);
        }
    });
}

function renderTenantChart(data) {
    const ctx = document.getElementById('tenantChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.map(item => new Date(item.date).toLocaleDateString()),
            datasets: [{
                label: 'New Tenants',
                data: data.map(item => item.count),
                backgroundColor: 'rgba(240, 147, 251, 0.8)',
                borderColor: '#f093fb',
                borderWidth: 2,
                borderRadius: 8
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
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0,0,0,0.1)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
}

function loadEmailChart() {
    $.ajax({
        url: '{{ route("landlord.dashboard.email-chart") }}',
        type: 'GET',
        success: function(response) {
            if (response.success) {
                renderEmailChart(response.data);
            }
        },
        error: function(xhr) {
            console.error('Error loading email chart:', xhr);
        }
    });
}

function renderEmailChart(data) {
    const ctx = document.getElementById('emailChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.map(item => new Date(item.date).toLocaleDateString()),
            datasets: [{
                label: 'Emails Sent',
                data: data.map(item => item.count),
                borderColor: '#4facfe',
                backgroundColor: 'rgba(79, 172, 254, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
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
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0,0,0,0.1)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
}

function loadModuleStats() {
    $.ajax({
        url: '{{ route("landlord.dashboard.module-stats") }}',
        type: 'GET',
        success: function(response) {
            if (response.success) {
                renderModuleStats(response.data);
            }
        },
        error: function(xhr) {
            console.error('Error loading module stats:', xhr);
        }
    });
}

function renderModuleStats(modules) {
    let html = '';
    
    Object.keys(modules).forEach(moduleName => {
        const moduleData = modules[moduleName];
        html += `
            <div class="col-xl-4 col-lg-6 col-md-6">
                <div class="module-stat-card">
                    <div class="module-name">${moduleName}</div>
        `;
        
        Object.keys(moduleData).forEach(statName => {
            html += `
                <div class="module-stat-item">
                    <span class="module-stat-label">${statName.charAt(0).toUpperCase() + statName.slice(1)}</span>
                    <span class="module-stat-value">${moduleData[statName]}</span>
                </div>
            `;
        });
        
        html += `
                </div>
            </div>
        `;
    });
    
    $('#module-stats').html(html);
}

function showError(message) {
    // You can implement a toast notification or alert here
    console.error(message);
}

// Handle clickable dashboard cards
$(document).on('click', '.clickable-card', function() {
    const module = $(this).data('module');
    
    if (module === 'brands') {
        // Navigate to brands page where super admin can select brand modules
        window.location.href = '{{ route("landlord.brands.index") }}';
    }
});
</script>
@endpush
