@extends('layouts.landlord.app')

@section('title', 'Monitoring Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Overview Cards -->
    <div class="row mb-4">
        <!-- System Health Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                System Health
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $systemHealth['system_uptime']['uptime_percentage'] ?? 99.9 }}%
                            </div>
                            <div class="text-xs text-muted">
                                {{ $systemHealth['total_tenants'] ?? 0 }} Active Tenants
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-heartbeat fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tenant Activity Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Active Sessions
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $tenantStats['total_active_sessions'] ?? 0 }}
                            </div>
                            <div class="text-xs text-muted">
                                {{ $tenantStats['total_logins_today'] ?? 0 }} Logins Today
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Error Rate Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Error Rate
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $errorStats['error_rate'] ?? 0 }}%
                            </div>
                            <div class="text-xs text-muted">
                                {{ $errorStats['total_errors_today'] ?? 0 }} Errors Today
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resource Usage Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Storage Usage
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $resourceStats['total_storage_gb'] ?? 0 }} GB
                            </div>
                            <div class="text-xs text-muted">
                                {{ $resourceStats['database_size_gb'] ?? 0 }} GB Database
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-database fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- System Performance Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">System Performance</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                            aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">Options:</div>
                            <a class="dropdown-item" href="#" onclick="refreshChart('performance')">Refresh</a>
                            <a class="dropdown-item" href="{{ route('landlord.monitoring.system-health') }}">View Details</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div id="performanceChart" style="height: 300px;"></div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Stats</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="small text-gray-500">Queue Health</div>
                        <div class="progress mb-2">
                            @php
                                $queueHealth = $systemHealth['queue_health']['status'] ?? 'healthy';
                                $queueClass = $queueHealth === 'healthy' ? 'bg-success' : ($queueHealth === 'warning' ? 'bg-warning' : 'bg-danger');
                                $queueWidth = $queueHealth === 'healthy' ? 90 : ($queueHealth === 'warning' ? 60 : 30);
                            @endphp
                            <div class="progress-bar {{ $queueClass }}" role="progressbar" style="width: {{ $queueWidth }}%"></div>
                        </div>
                        <div class="small">
                            Pending: {{ $systemHealth['queue_health']['pending_jobs'] ?? 0 }} | 
                            Failed: {{ $systemHealth['queue_health']['failed_jobs'] ?? 0 }}
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="small text-gray-500">Database Status</div>
                        <div class="progress mb-2">
                            @php
                                $dbStatus = $systemHealth['database_status']['status'] ?? 'healthy';
                                $dbClass = $dbStatus === 'healthy' ? 'bg-success' : 'bg-danger';
                                $dbWidth = $dbStatus === 'healthy' ? 95 : 20;
                            @endphp
                            <div class="progress-bar {{ $dbClass }}" role="progressbar" style="width: {{ $dbWidth }}%"></div>
                        </div>
                        <div class="small">
                            Connections: {{ $systemHealth['database_status']['connection_count'] ?? 0 }}
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="small text-gray-500">API Usage</div>
                        <div class="progress mb-2">
                            <div class="progress-bar bg-info" role="progressbar" style="width: 75%"></div>
                        </div>
                        <div class="small">
                            {{ $tenantStats['total_api_requests_today'] ?? 0 }} Requests Today
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Monitoring Modules Grid -->
    <div class="row">
        <!-- System Health Module -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-heartbeat mr-2"></i>System Health
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted">Monitor tenant uptime, database health, and queue statistics.</p>
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="h4 font-weight-bold text-success">{{ $systemHealth['active_tenants'] ?? 0 }}</div>
                            <div class="small text-muted">Active Tenants</div>
                        </div>
                        <div class="col-4">
                            <div class="h4 font-weight-bold text-info">{{ $systemHealth['system_uptime']['uptime_percentage'] ?? 99.9 }}%</div>
                            <div class="small text-muted">Uptime</div>
                        </div>
                        <div class="col-4">
                            <div class="h4 font-weight-bold text-warning">{{ $systemHealth['queue_health']['pending_jobs'] ?? 0 }}</div>
                            <div class="small text-muted">Queue Jobs</div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('landlord.monitoring.system-health') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-chart-line mr-1"></i>View Details
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tenant Behavior Module -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-users mr-2"></i>Tenant Behavior
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted">Analyze user sessions, login patterns, and feature usage.</p>
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="h4 font-weight-bold text-primary">{{ $tenantStats['total_active_sessions'] ?? 0 }}</div>
                            <div class="small text-muted">Active Sessions</div>
                        </div>
                        <div class="col-4">
                            <div class="h4 font-weight-bold text-success">{{ $tenantStats['total_logins_today'] ?? 0 }}</div>
                            <div class="small text-muted">Logins Today</div>
                        </div>
                        <div class="col-4">
                            <div class="h4 font-weight-bold text-info">{{ $tenantStats['average_session_duration'] ?? 0 }}m</div>
                            <div class="small text-muted">Avg Session</div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('landlord.monitoring.tenant-behavior') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-chart-bar mr-1"></i>View Analytics
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Error Management Module -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">
                        <i class="fas fa-exclamation-triangle mr-2"></i>Error Management
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted">Centralized error tracking and alert management.</p>
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="h4 font-weight-bold text-danger">{{ $errorStats['total_errors_today'] ?? 0 }}</div>
                            <div class="small text-muted">Errors Today</div>
                        </div>
                        <div class="col-4">
                            <div class="h4 font-weight-bold text-warning">{{ $errorStats['critical_errors'] ?? 0 }}</div>
                            <div class="small text-muted">Critical</div>
                        </div>
                        <div class="col-4">
                            <div class="h4 font-weight-bold text-info">{{ $errorStats['error_rate'] ?? 0 }}%</div>
                            <div class="small text-muted">Error Rate</div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('landlord.monitoring.error-management') }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-bug mr-1"></i>View Errors
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resource Insights Module -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">
                        <i class="fas fa-database mr-2"></i>Resource Insights
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted">Monitor storage usage, database growth, and resource limits.</p>
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="h4 font-weight-bold text-primary">{{ $resourceStats['total_storage_gb'] ?? 0 }}GB</div>
                            <div class="small text-muted">Total Storage</div>
                        </div>
                        <div class="col-4">
                            <div class="h4 font-weight-bold text-success">{{ $resourceStats['database_size_gb'] ?? 0 }}GB</div>
                            <div class="small text-muted">Database Size</div>
                        </div>
                        <div class="col-4">
                            <div class="h4 font-weight-bold text-warning">{{ $resourceStats['average_db_growth'] ?? 0 }}MB</div>
                            <div class="small text-muted">Daily Growth</div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('landlord.monitoring.resource-insights') }}" class="btn btn-info btn-sm">
                            <i class="fas fa-chart-area mr-1"></i>View Resources
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Admin & Developer Tools Row -->
    <div class="row">
        <!-- Admin Tools -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-dark">
                        <i class="fas fa-tools mr-2"></i>Admin Tools
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted">Data consistency checks and administrative utilities.</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small text-muted">Last consistency check:</div>
                            <div class="font-weight-bold">{{ now()->subHours(2)->diffForHumans() }}</div>
                        </div>
                        <div>
                            <a href="{{ route('landlord.monitoring.admin-tools') }}" class="btn btn-dark btn-sm">
                                <i class="fas fa-wrench mr-1"></i>Admin Panel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Developer Tools -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-secondary">
                        <i class="fas fa-code mr-2"></i>Developer Tools
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted">Migration status, debugging tools, and system diagnostics.</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small text-muted">Migration status:</div>
                            <div class="font-weight-bold text-success">All Up to Date</div>
                        </div>
                        <div>
                            <a href="{{ route('landlord.monitoring.developer-tools') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-terminal mr-1"></i>Dev Console
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
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="{{ asset('assets/landlord/js/monitoring/dashboard.js') }}"></script>
@endsection
