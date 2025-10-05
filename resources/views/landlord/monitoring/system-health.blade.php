@extends('layouts.landlord.app')

@section('title', 'System Health Monitoring')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">@translate('system_health_monitoring')</h1>
        <div>
            <button class="btn btn-primary btn-sm" onclick="refreshAllData()">
                <i class="fas fa-sync-alt mr-1"></i>Refresh All
            </button>
            <button class="btn btn-success btn-sm" onclick="runHealthCheck()">
                <i class="fas fa-heartbeat mr-1"></i>Run Health Check
            </button>
        </div>
    </div>

    <!-- System Overview Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">@translate('system_uptime')</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="system-uptime">99.9%</div>
                            <div class="text-xs text-muted">@translate('last_30_days')</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-server fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Database Health</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="db-status">Healthy</div>
                            <div class="text-xs text-muted">All connections active</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-database fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Queue Status</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="queue-status">Processing</div>
                            <div class="text-xs text-muted"><span id="pending-jobs">0</span> pending jobs</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tasks fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Active Tenants</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="active-tenants">0</div>
                            <div class="text-xs text-muted">Currently online</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-building fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- System Performance Chart -->
        <div class="col-xl-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Real-Time System Performance</h6>
                </div>
                <div class="card-body">
                    <div id="systemPerformanceChart" style="height: 350px;"></div>
                </div>
            </div>
        </div>

        <!-- System Resources -->
        <div class="col-xl-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">System Resources</h6>
                </div>
                <div class="card-body">
                    <!-- CPU Usage -->
                    <div class="mb-4">
                        <div class="small text-gray-500 mb-1">CPU Usage</div>
                        <div class="progress mb-1">
                            <div class="progress-bar bg-primary" id="cpu-progress" role="progressbar" style="width: 45%"></div>
                        </div>
                        <div class="small"><span id="cpu-percentage">45</span>% - <span id="cpu-load">Normal Load</span></div>
                    </div>

                    <!-- Memory Usage -->
                    <div class="mb-4">
                        <div class="small text-gray-500 mb-1">Memory Usage</div>
                        <div class="progress mb-1">
                            <div class="progress-bar bg-success" id="memory-progress" role="progressbar" style="width: 60%"></div>
                        </div>
                        <div class="small"><span id="memory-used">4.8</span>GB / <span id="memory-total">8</span>GB</div>
                    </div>

                    <!-- Disk Usage -->
                    <div class="mb-4">
                        <div class="small text-gray-500 mb-1">Disk Usage</div>
                        <div class="progress mb-1">
                            <div class="progress-bar bg-info" id="disk-progress" role="progressbar" style="width: 35%"></div>
                        </div>
                        <div class="small"><span id="disk-used">175</span>GB / <span id="disk-total">500</span>GB</div>
                    </div>

                    <!-- Network -->
                    <div class="mb-3">
                        <div class="small text-gray-500 mb-1">Network Activity</div>
                        <div class="d-flex justify-content-between">
                            <span class="small">↑ <span id="network-up">125</span> MB/s</span>
                            <span class="small">↓ <span id="network-down">89</span> MB/s</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tenant Health Status -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Tenant Health Status</h6>
                    <button class="btn btn-sm btn-outline-primary" onclick="refreshTenantHealth()">
                        <i class="fas fa-sync-alt mr-1"></i>Refresh
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="tenantHealthTable">
                            <thead>
                                <tr>
                                    <th>Tenant</th>
                                    <th>Domain</th>
                                    <th>Status</th>
                                    <th>Response Time</th>
                                    <th>Database</th>
                                    <th>Last Activity</th>
                                    <th>Health Score</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="tenantHealthBody">
                                <!-- Data will be loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Database Health Details -->
    <div class="row mb-4">
        <div class="col-xl-6">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Database Health</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Database</th>
                                    <th>Size</th>
                                    <th>Tables</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="databaseHealthBody">
                                <!-- Data will be loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Queue Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-3">
                            <div class="h4 font-weight-bold text-primary" id="pending-jobs-count">0</div>
                            <div class="small text-muted">Pending</div>
                        </div>
                        <div class="col-3">
                            <div class="h4 font-weight-bold text-success" id="completed-jobs-count">0</div>
                            <div class="small text-muted">Completed</div>
                        </div>
                        <div class="col-3">
                            <div class="h4 font-weight-bold text-danger" id="failed-jobs-count">0</div>
                            <div class="small text-muted">Failed</div>
                        </div>
                        <div class="col-3">
                            <div class="h4 font-weight-bold text-info" id="active-workers-count">0</div>
                            <div class="small text-muted">Workers</div>
                        </div>
                    </div>
                    <hr>
                    <div class="small text-muted">
                        <div>Average Processing Time: <span id="avg-processing-time" class="font-weight-bold">0ms</span></div>
                        <div>Queue Health: <span id="queue-health-status" class="font-weight-bold text-success">Healthy</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Health Check Modal -->
<div class="modal fade" id="healthCheckModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">System Health Check</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="healthCheckResults">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Running health check...</span>
                        </div>
                        <p class="mt-2">Running comprehensive health check...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="{{ asset('assets/landlord/js/monitoring/system-health.js') }}"></script>
@endsection
