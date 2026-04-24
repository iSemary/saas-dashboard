@extends('layouts.landlord.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="col-6">

                        <h3>System Status Check</h3>
                    </div>
                    <div class="col-6 text-revert">
                        <span id="last-updated" class="me-3">Last updated: {{ now()->format('H:i:s') }}</span>
                        <button class="btn btn-sm btn-primary" id="refresh-btn">Refresh</button>
                    </div>
                </div>

                <div class="card-body">
                    <div id="system-status-container">
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <span class="h5">Overall Status:</span>
                                        <span id="overall-status" class="badge bg-success ms-2">All Systems
                                            Operational</span>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="auto-refresh">
                                        <label class="form-check-label" for="auto-refresh">Auto refresh (30s)</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Service</th>
                                        <th>Status</th>
                                        <th>Message</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="status-table-body">
                                    @foreach ($statuses as $key => $status)
                                        <tr id="service-{{ $key }}" data-service="{{ $key }}">
                                            <td><strong>{{ ucwords(str_replace('_', ' ', $key)) }}</strong></td>
                                            <td>
                                                <span
                                                    class="badge bg-{{ $status['status'] === 'success' ? 'success' : ($status['status'] === 'warning' ? 'warning' : 'danger') }}">
                                                    @if ($key == 'database')
                                                        {{ $status['status'] === 'success' ? 'Connected' : 'Failed' }}
                                                    @elseif($key == 'websocket')
                                                        {{ $status['status'] === 'success' ? 'Running' : 'Not Running' }}
                                                    @elseif($key == 'queue')
                                                        {{ $status['status'] === 'success' ? 'Running' : ($status['status'] === 'warning' ? 'Warning' : 'Error') }}
                                                    @elseif($key == 'redis')
                                                        {{ $status['status'] === 'success' ? 'Connected' : 'Failed' }}
                                                    @elseif($key == 'supervisor')
                                                        {{ $status['status'] === 'success' ? 'Running' : 'Not Running' }}
                                                    @elseif($key == 'disk_space')
                                                        {{ $status['status'] === 'success' ? 'Sufficient' : 'Low' }}
                                                    @elseif($key == 'php_version')
                                                        {{ $status['status'] === 'success' ? 'Compatible' : 'Not Compatible' }}
                                                    @elseif($key == 'memory_usage')
                                                        {{ $status['status'] === 'success' ? 'Normal' : 'High Usage' }}
                                                    @elseif($key == 'cache_status')
                                                        {{ $status['status'] === 'success' ? 'Enabled' : 'Disabled' }}
                                                    @endif
                                                </span>
                                            </td>
                                            <td>{{ $status['message'] }}</td>
                                            <td>
                                                @if ($key == 'cache_status')
                                                    <button class="btn btn-sm btn-outline-info clear-cache-btn">Clear
                                                        Cache</button>
                                                @elseif($key == 'queue')
                                                    <button class="btn btn-sm btn-outline-info restart-queue-btn">Restart
                                                        Queues</button>
                                                @elseif($key == 'supervisor')
                                                    <button
                                                        class="btn btn-sm btn-outline-info restart-supervisor-btn">Restart
                                                        Supervisor</button>
                                                @else
                                                    <button
                                                        class="btn btn-sm btn-outline-secondary check-individual-btn">Check</button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="text-center d-none">
                        <div class="text-primary" role="status">
                            <i class="fas fa-circle-notch fa-spin"></i>
                        </div>
                        <p class="mt-2">Checking system status...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            let autoRefreshInterval;
            let isRefreshing = false;

            // Update overall status
            function updateOverallStatus() {
                const errorCount = $('.badge.bg-danger').length;
                const warningCount = $('.badge.bg-warning').length;

                const $overallStatus = $('#overall-status');

                if (errorCount > 0) {
                    $overallStatus.removeClass('bg-success bg-warning').addClass('bg-danger');
                    $overallStatus.text(`System Errors Detected (${errorCount})`);
                } else if (warningCount > 0) {
                    $overallStatus.removeClass('bg-success bg-danger').addClass('bg-warning');
                    $overallStatus.text(`System Warnings (${warningCount})`);
                } else {
                    $overallStatus.removeClass('bg-warning bg-danger').addClass('bg-success');
                    $overallStatus.text('All Systems Operational');
                }
            }

            // Refresh system status
            function refreshStatus() {
                if (isRefreshing) return;

                isRefreshing = true;
                $('#system-status-container').addClass('d-none');
                $('#loading-spinner').removeClass('d-none');

                $.ajax({
                    url: '{{ route('landlord.development.system_status.check') }}',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        updateStatusTable(response);
                        $('#last-updated').text('Last updated: ' + new Date().toLocaleTimeString());
                        updateOverallStatus();

                        isRefreshing = false;
                        $('#loading-spinner').addClass('d-none');
                        $('#system-status-container').removeClass('d-none');
                    },
                    error: function(error) {
                        console.error('Error refreshing status:', error);
                        isRefreshing = false;
                        $('#loading-spinner').addClass('d-none');
                        $('#system-status-container').removeClass('d-none');
                        showToast('error', 'Failed to refresh system status.');
                    }
                });
            }

            // Update status table
            function updateStatusTable(data) {
                Object.keys(data).forEach(key => {
                    const status = data[key];
                    const $row = $(`#service-${key}`);

                    if ($row.length) {
                        let statusText = '';

                        // Determine status text based on service
                        if (key === 'database') {
                            statusText = status.status === 'success' ? 'Connected' : 'Failed';
                        } else if (key === 'websocket') {
                            statusText = status.status === 'success' ? 'Running' : 'Not Running';
                        } else if (key === 'queue') {
                            statusText = status.status === 'success' ? 'Running' : (status.status ===
                                'warning' ? 'Warning' : 'Error');
                        } else if (key === 'redis') {
                            statusText = status.status === 'success' ? 'Connected' : 'Failed';
                        } else if (key === 'supervisor') {
                            statusText = status.status === 'success' ? 'Running' : 'Not Running';
                        } else if (key === 'disk_space') {
                            statusText = status.status === 'success' ? 'Sufficient' : 'Low';
                        } else if (key === 'php_version') {
                            statusText = status.status === 'success' ? 'Compatible' : 'Not Compatible';
                        } else if (key === 'memory_usage') {
                            statusText = status.status === 'success' ? 'Normal' : 'High Usage';
                        } else if (key === 'cache_status') {
                            statusText = status.status === 'success' ? 'Enabled' : 'Disabled';
                        }

                        // Update badge status
                        const badgeClass = status.status === 'success' ? 'bg-success' : (status.status ===
                            'warning' ? 'bg-warning' : 'bg-danger');
                        $row.find('td:nth-child(2) span').removeClass('bg-success bg-warning bg-danger')
                            .addClass(badgeClass).text(statusText);

                        // Update message
                        $row.find('td:nth-child(3)').text(status.message);
                    }
                });
            }

            // Initialize auto refresh
            function startAutoRefresh() {
                stopAutoRefresh();
                autoRefreshInterval = setInterval(refreshStatus, 30000);
            }

            function stopAutoRefresh() {
                if (autoRefreshInterval) {
                    clearInterval(autoRefreshInterval);
                }
            }

            // Event handlers
            $('#refresh-btn').on('click', function() {
                refreshStatus();
            });

            $('#auto-refresh').on('change', function() {
                if ($(this).is(':checked')) {
                    startAutoRefresh();
                } else {
                    stopAutoRefresh();
                }
            });

            // Individual service checks
            $('.check-individual-btn').on('click', function() {
                const $row = $(this).closest('tr');
                const service = $row.data('service');

                $(this).prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>'
                    );

                $.ajax({
                    url: `{{ route('landlord.development.system_status.check_service') }}`,
                    type: 'POST',
                    data: {
                        service: service,
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: 'json',
                    success: function(response) {
                        const status = response.status;
                        let statusText = '';

                        // Determine status text based on service
                        if (service === 'database') {
                            statusText = status.status === 'success' ? 'Connected' : 'Failed';
                        } else if (service === 'websocket') {
                            statusText = status.status === 'success' ? 'Running' :
                            'Not Running';
                        } else if (service === 'queue') {
                            statusText = status.status === 'success' ? 'Running' : (status
                                .status === 'warning' ? 'Warning' : 'Error');
                        } else if (service === 'redis') {
                            statusText = status.status === 'success' ? 'Connected' : 'Failed';
                        } else if (service === 'supervisor') {
                            statusText = status.status === 'success' ? 'Running' :
                            'Not Running';
                        } else if (service === 'disk_space') {
                            statusText = status.status === 'success' ? 'Sufficient' : 'Low';
                        } else if (service === 'php_version') {
                            statusText = status.status === 'success' ? 'Compatible' :
                                'Not Compatible';
                        } else if (service === 'memory_usage') {
                            statusText = status.status === 'success' ? 'Normal' : 'High Usage';
                        } else if (service === 'cache_status') {
                            statusText = status.status === 'success' ? 'Enabled' : 'Disabled';
                        }

                        const badgeClass = status.status === 'success' ? 'bg-success' : (status
                            .status === 'warning' ? 'bg-warning' : 'bg-danger');
                        $row.find('td:nth-child(2) span').removeClass(
                            'bg-success bg-warning bg-danger').addClass(badgeClass).text(
                            statusText);
                        $row.find('td:nth-child(3)').text(status.message);

                        updateOverallStatus();
                        showToast('success', `${service.replace('_', ' ')} check completed`);
                    },
                    error: function(error) {
                        console.error('Error checking service:', error);
                        showToast('error', `Failed to check ${service.replace('_', ' ')}`);
                    },
                    complete: function() {
                        $row.find('.check-individual-btn').prop('disabled', false).html(
                        'Check');
                    }
                });
            });

            // Service action buttons
            $('.clear-cache-btn').on('click', function() {
                const $btn = $(this);
                $btn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>'
                    );

                $.ajax({
                    url: '{{ route('landlord.development.system_status.clear_cache') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: 'json',
                    success: function(response) {
                        showToast('success', 'Cache cleared successfully');
                        refreshStatus();
                    },
                    error: function(error) {
                        console.error('Error clearing cache:', error);
                        showToast('error', 'Failed to clear cache');
                    },
                    complete: function() {
                        $btn.prop('disabled', false).html('Clear Cache');
                    }
                });
            });

            $('.restart-queue-btn').on('click', function() {
                const $btn = $(this);
                $btn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>'
                    );

                $.ajax({
                    url: '{{ route('landlord.development.system_status.restart_queue') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: 'json',
                    success: function(response) {
                        showToast('success', 'Queue restarted successfully');
                        refreshStatus();
                    },
                    error: function(error) {
                        console.error('Error restarting queue:', error);
                        showToast('error', 'Failed to restart queue');
                    },
                    complete: function() {
                        $btn.prop('disabled', false).html('Restart Queues');
                    }
                });
            });

            $('.restart-supervisor-btn').on('click', function() {
                const $btn = $(this);
                $btn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>'
                    );

                $.ajax({
                    url: '{{ route('landlord.development.system_status.restart_supervisor') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: 'json',
                    success: function(response) {
                        showToast('success', 'Supervisor restarted successfully');
                        refreshStatus();
                    },
                    error: function(error) {
                        console.error('Error restarting supervisor:', error);
                        showToast('error', 'Failed to restart supervisor');
                    },
                    complete: function() {
                        $btn.prop('disabled', false).html('Restart Supervisor');
                    }
                });
            });

            // Initial update
            updateOverallStatus();

            // Start auto refresh if enabled
            if ($('#auto-refresh').is(':checked')) {
                startAutoRefresh();
            }
        });
    </script>
@endpush
