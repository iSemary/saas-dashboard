@extends('layouts.tenant.app')

@section('title', translate('dashboard'))

@section('content')
<div class="container-fluid">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="mb-2">@translate('welcome_back'), {{ $user->name }}!</h2>
            <p class="text-muted">@translate('heres_whats_happening_with_your_business_today')</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3 id="total-users">{{ $stats['overview']['total_users'] ?? 0 }}</h3>
                    <p>@translate('total_users')</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <a href="{{ route('tenant.users.index') }}" class="small-box-footer">
                    @translate('more_info') <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3 id="active-users">{{ $stats['overview']['active_users'] ?? 0 }}</h3>
                    <p>@translate('active_users')</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-check"></i>
                </div>
                <a href="{{ route('tenant.users.index') }}" class="small-box-footer">
                    @translate('more_info') <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3 id="total-projects">{{ $stats['overview']['total_projects'] ?? 0 }}</h3>
                    <p>@translate('projects')</p>
                </div>
                <div class="icon">
                    <i class="fas fa-folder"></i>
                </div>
                <a href="#" class="small-box-footer">
                    @translate('more_info') <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3 id="growth-rate">{{ $stats['overview']['growth_rate'] ?? 0 }}%</h3>
                    <p>@translate('growth_rate')</p>
                </div>
                <div class="icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <a href="#" class="small-box-footer">
                    @translate('more_info') <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">@translate('revenue_trend')</h3>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" height="200"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">@translate('user_growth')</h3>
                </div>
                <div class="card-body">
                    <canvas id="userGrowthChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">@translate('quick_actions')</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 col-sm-6">
                            <a href="{{ route('tenant.users.create') }}" class="btn btn-app bg-primary btn-block">
                                <i class="fas fa-user-plus"></i> @translate('add_user')
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <a href="{{ route('tenant.roles.create') }}" class="btn btn-app bg-success btn-block">
                                <i class="fas fa-user-shield"></i> @translate('add_role')
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <a href="{{ route('tenant.permissions.create') }}" class="btn btn-app bg-warning btn-block">
                                <i class="fas fa-key"></i> @translate('add_permission')
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <a href="{{ route('tenant.settings.index') }}" class="btn btn-app bg-info btn-block">
                                <i class="fas fa-cog"></i> @translate('settings')
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <a href="{{ route('tenant.branches.index') }}" class="btn btn-app bg-secondary btn-block">
                                <i class="fas fa-building"></i> @translate('branches')
                            </a>
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
    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart');
    if (revenueCtx)
    {
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: '@translate("revenue")',
                    data: [12, 19, 3, 5, 2, 3],
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true
            }
        });
    }

    // User Growth Chart
    const userGrowthCtx = document.getElementById('userGrowthChart');
    if (userGrowthCtx)
    {
        new Chart(userGrowthCtx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: '@translate("new_users")',
                    data: [5, 10, 8, 15, 12, 20],
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgb(54, 162, 235)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
});
</script>
@endsection
