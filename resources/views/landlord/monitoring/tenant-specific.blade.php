@extends('layouts.landlord.app')

@section('title', $title)

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ $tenant->name }} - @translate('monitoring')</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">@translate('home')</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('landlord.monitoring.index') }}">@translate('monitoring')</a></li>
                        <li class="breadcrumb-item active">{{ $tenant->name }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            @php
                // Safe fallbacks for missing service data
                $systemHealth = $systemHealth ?? [];
                $behavior = $behavior ?? [];
                $errors = $errors ?? [];
                $resources = $resources ?? [];
            @endphp

            <!-- Tenant Info Card -->
            <div class="row">
                <div class="col-12">
                    <div class="card card-outline card-primary">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-chart-line mr-2"></i>@translate('tenant_information')
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Domain:</strong> {{ $tenant->domain }}</p>
                                    <p><strong>Database:</strong> {{ $tenant->database }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Status:</strong>
                                        <span class="badge badge-success">{{ ucfirst($tenant->status ?? 'Active') }}</span>
                                    </p>
                                    <p><strong>Created:</strong> {{ $tenant->created_at->format('M d, Y') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row">
                <!-- System Health Card -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ ucfirst($systemHealth['status'] ?? 'Active') }}</h3>
                            <p>@translate('tenant_status')</p>
                            <small>Health Score: {{ $systemHealth['health_score'] ?? 'N/A' }}</small>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>

                <!-- Database Size Card -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ number_format($resources['database_size_mb'] ?? 0, 1) }}<sup
                                    style="font-size: 20px">MB</sup></h3>
                            <p>@translate('database_size')</p>
                            <small>{{ $resources['table_count'] ?? 0 }}/{{ $resources['expected_tables'] ?? 'N/A' }}
                                @translate('tables')</small>
                        </div>
                        <div class="icon">
                            <i class="fas fa-database"></i>
                        </div>
                    </div>
                </div>

                <!-- Sessions Card -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ $behavior['login_activity']['logins_today'] ?? 0 }}</h3>
                            <p>@translate('sessions_logins')</p>
                            <small>{{ $behavior['active_sessions']['count'] ?? 0 }} @translate('active_sessions')</small>
                        </div>
                        <div class="icon">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>

                <!-- Errors Card -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>{{ $errors['error_count_today'] ?? 0 }}</h3>
                            <p>@translate('recent_errors')</p>
                            <small>{{ $errors['critical_errors'] ?? 0 }} @translate('critical')</small>
                        </div>
                        <div class="icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="row">
                <div class="col-12">
                    <div class="card card-outline card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-tools mr-2"></i>@translate('quick_actions')
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <button type="button" class="btn btn-warning btn-block"
                                        onclick="tenantRemigrate({{ $tenant->id }})">
                                        <i class="fas fa-database mr-2"></i>@translate('re_migrate_database')
                                    </button>
                                </div>
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <button type="button" class="btn btn-primary btn-block"
                                        onclick="tenantSeed({{ $tenant->id }})">
                                        <i class="fas fa-seedling mr-2"></i>@translate('seed_database')
                                    </button>
                                </div>
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <button type="button" class="btn btn-secondary btn-block"
                                        onclick="tenantReseed({{ $tenant->id }})">
                                        <i class="fas fa-redo mr-2"></i>@translate('re_seed_database')
                                    </button>
                                </div>
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <a href="{{ route('landlord.tenants.edit', $tenant->id) }}"
                                        class="btn btn-info btn-block">
                                        <i class="fas fa-edit mr-2"></i>@translate('edit_tenant')
                                    </a>
                                </div>
                            </div>
                            <hr>
                            <a href="{{ route('landlord.tenants.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left mr-2"></i>@translate('back_to_tenants')
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Information -->
            <div class="row">
                <!-- System Health Details -->
                <div class="col-md-6">
                    <div class="card card-outline card-success">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-heartbeat mr-2"></i>@translate('system_health_details')
                            </h3>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-unbordered mb-3">
                                <li class="list-group-item">
                                    <b>@translate('status')</b>
                                    <span
                                        class="badge badge-{{ ($systemHealth['status'] ?? 'active') === 'active' ? 'success' : 'warning' }} float-right">
                                        {{ ucfirst($systemHealth['status'] ?? 'Active') }}
                                    </span>
                                </li>
                                <li class="list-group-item">
                                    <b>@translate('last_check')</b>
                                    <span class="float-right">{{ $systemHealth['last_check'] ?? 'Just now' }}</span>
                                </li>
                                <li class="list-group-item">
                                    <b>@translate('response_time')</b>
                                    <span class="float-right">{{ $systemHealth['response_time'] ?? 'N/A' }}ms</span>
                                </li>
                                <li class="list-group-item">
                                    <b>@translate('database_status')</b>
                                    <span
                                        class="badge badge-{{ ($systemHealth['database_status'] ?? 'healthy') === 'healthy' ? 'success' : 'danger' }} float-right">
                                        {{ ucfirst($systemHealth['database_status'] ?? 'Healthy') }}
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Resource Usage Details -->
                <div class="col-md-6">
                    <div class="card card-outline card-info">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-chart-area mr-2"></i>@translate('resource_usage')
                            </h3>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-unbordered mb-3">
                                <li class="list-group-item">
                                    <b>@translate('database_size')</b>
                                    <span class="float-right">{{ number_format($resources['database_size_mb'] ?? 0, 1) }}
                                        MB</span>
                                </li>
                                <li class="list-group-item">
                                    <b>@translate('table_count')</b>
                                    <span class="float-right">{{ $resources['table_count'] ?? 0 }} /
                                        {{ $resources['expected_tables'] ?? 'N/A' }}</span>
                                </li>
                                <li class="list-group-item">
                                    <b>@translate('storage_used')</b>
                                    <span class="float-right">{{ number_format($resources['storage_used_mb'] ?? 0, 1) }}
                                        MB</span>
                                </li>
                                <li class="list-group-item">
                                    <b>@translate('growth_rate')</b>
                                    <span class="float-right">{{ $resources['daily_growth_mb'] ?? 0 }} MB/day</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Performance Chart Row -->
            <div class="row">
                <div class="col-12">
                    <div class="card card-outline card-primary">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-chart-line mr-2"></i>{{ $tenant->name }} @translate('performance_metrics')
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" onclick="refreshTenantChart()">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="tenantPerformanceChart" style="height: 300px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Tenant Performance Chart
        const ctx = document.getElementById('tenantPerformanceChart').getContext('2d');
        const tenantChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['1h ago', '50m ago', '40m ago', '30m ago', '20m ago', '10m ago'],
                datasets: [{
                    label: 'Response Time (ms)',
                    data: [{{ $systemHealth['response_time'] ?? 150 }},
                        {{ $systemHealth['response_time'] ?? 120 }},
                        {{ $systemHealth['response_time'] ?? 180 }},
                        {{ $systemHealth['response_time'] ?? 160 }},
                        {{ $systemHealth['response_time'] ?? 140 }},
                        {{ $systemHealth['response_time'] ?? 155 }}
                    ],
                    borderColor: 'rgb(0, 123, 255)',
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Response Time (ms)'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Time'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                }
            }
        });

        // Placeholder functions for quick actions
        function tenantRemigrate(tenantId) {
            if (confirm('Are you sure you want to re-migrate the database for tenant ' + tenantId + '?')) {
                // Implementation would go here
                alert('Re-migrate functionality is yet to be implemented');
            }
        }

        function tenantSeed(tenantId) {
            if (confirm('Are you sure you want to seed the database for tenant ' + tenantId + '?')) {
                // Implementation would go here
                alert('Seed functionality is yet to be implemented');
            }
        }

        function tenantReseed(tenantId) {
            if (confirm('Are you sure you want to re-seed the database for tenant ' + tenantId +
                    '? This will clear existing data.')) {
                // Implementation would go here
                alert('Re-seed functionality is yet to be implemented');
            }
        }

        function refreshTenantChart() {
            tenantChart.update();
        }
    </script>
@endsection
