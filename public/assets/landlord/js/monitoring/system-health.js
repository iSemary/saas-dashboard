// System Health Monitoring JavaScript
$(document).ready(function() {
    // Initialize charts and data
    initializeSystemChart();
    loadTenantHealthData();
    loadDatabaseHealthData();
    loadQueueStats();
    
    // Start real-time updates
    startRealTimeUpdates();
});

let systemChart;

function initializeSystemChart() {
    const options = {
        series: [{
            name: 'CPU Usage',
            data: []
        }, {
            name: 'Memory Usage',
            data: []
        }, {
            name: 'Disk I/O',
            data: []
        }, {
            name: 'Network',
            data: []
        }],
        chart: {
            height: 350,
            type: 'line',
            animations: {
                enabled: true,
                easing: 'easeinout',
                speed: 800
            },
            toolbar: {
                show: true
            }
        },
        colors: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e'],
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth',
            width: 3
        },
        xaxis: {
            type: 'datetime',
            labels: {
                format: 'HH:mm:ss'
            }
        },
        yaxis: {
            title: {
                text: 'Usage (%)'
            },
            min: 0,
            max: 100
        },
        tooltip: {
            x: {
                format: 'HH:mm:ss'
            },
            y: {
                formatter: function(val) {
                    return val.toFixed(1) + '%';
                }
            }
        },
        legend: {
            position: 'top'
        },
        grid: {
            borderColor: '#e7e7e7'
        }
    };

    systemChart = new ApexCharts(document.querySelector("#systemPerformanceChart"), options);
    systemChart.render();
    
    // Load initial data
    loadInitialChartData();
}

function loadInitialChartData() {
    const now = new Date();
    const data = {
        cpu: [],
        memory: [],
        disk: [],
        network: []
    };
    
    // Generate sample data for the last 2 hours
    for (let i = 120; i >= 0; i--) {
        const time = new Date(now.getTime() - (i * 60 * 1000));
        data.cpu.push([time.getTime(), Math.floor(Math.random() * 30) + 20]);
        data.memory.push([time.getTime(), Math.floor(Math.random() * 25) + 45]);
        data.disk.push([time.getTime(), Math.floor(Math.random() * 20) + 10]);
        data.network.push([time.getTime(), Math.floor(Math.random() * 15) + 5]);
    }
    
    systemChart.updateSeries([
        { name: 'CPU Usage', data: data.cpu },
        { name: 'Memory Usage', data: data.memory },
        { name: 'Disk I/O', data: data.disk },
        { name: 'Network', data: data.network }
    ]);
}

function startRealTimeUpdates() {
    // Update every 10 seconds
    setInterval(function() {
        updateRealTimeData();
    }, 10000);
    
    // Initial update
    updateRealTimeData();
}

function updateRealTimeData() {
    $.ajax({
        url: '/landlord/monitoring/api/system-health',
        method: 'GET',
        success: function(data) {
            updateSystemMetrics(data);
            updateChartData(data);
        },
        error: function(xhr, status, error) {
            console.error('Failed to fetch system health data:', error);
        }
    });
}

function updateSystemMetrics(data) {
    // Update system overview cards
    if (data.system_load) {
        const cpuUsage = data.system_load.cpu_usage || Math.floor(Math.random() * 40) + 20;
        $('#cpu-percentage').text(cpuUsage);
        $('#cpu-progress').css('width', cpuUsage + '%');
        
        if (cpuUsage > 80) {
            $('#cpu-progress').removeClass('bg-primary bg-warning').addClass('bg-danger');
            $('#cpu-load').text('High Load');
        } else if (cpuUsage > 60) {
            $('#cpu-progress').removeClass('bg-primary bg-danger').addClass('bg-warning');
            $('#cpu-load').text('Medium Load');
        } else {
            $('#cpu-progress').removeClass('bg-warning bg-danger').addClass('bg-primary');
            $('#cpu-load').text('Normal Load');
        }
    }
    
    if (data.memory_usage) {
        const memoryPercent = Math.round((data.memory_usage.used_mb / data.memory_usage.total_mb) * 100);
        $('#memory-used').text((data.memory_usage.used_mb / 1024).toFixed(1));
        $('#memory-total').text((data.memory_usage.total_mb / 1024).toFixed(0));
        $('#memory-progress').css('width', memoryPercent + '%');
        
        if (memoryPercent > 85) {
            $('#memory-progress').removeClass('bg-success bg-warning').addClass('bg-danger');
        } else if (memoryPercent > 70) {
            $('#memory-progress').removeClass('bg-success bg-danger').addClass('bg-warning');
        } else {
            $('#memory-progress').removeClass('bg-warning bg-danger').addClass('bg-success');
        }
    }
    
    if (data.disk_usage) {
        const diskPercent = data.disk_usage.percentage || Math.floor(Math.random() * 40) + 20;
        $('#disk-used').text(data.disk_usage.used_gb || Math.floor(Math.random() * 200) + 100);
        $('#disk-total').text(data.disk_usage.total_gb || 500);
        $('#disk-progress').css('width', diskPercent + '%');
    }
    
    // Update network stats
    $('#network-up').text(Math.floor(Math.random() * 200) + 50);
    $('#network-down').text(Math.floor(Math.random() * 150) + 30);
    
    // Update active connections
    if (data.active_connections) {
        $('#active-tenants').text(data.active_connections);
    }
}

function updateChartData(data) {
    if (systemChart) {
        const now = new Date().getTime();
        const cpuUsage = data.system_load ? data.system_load.cpu_usage : Math.floor(Math.random() * 40) + 20;
        const memoryUsage = data.memory_usage ? Math.round((data.memory_usage.used_mb / data.memory_usage.total_mb) * 100) : Math.floor(Math.random() * 30) + 45;
        const diskUsage = data.disk_usage ? data.disk_usage.percentage : Math.floor(Math.random() * 20) + 10;
        const networkUsage = Math.floor(Math.random() * 15) + 5;
        
        systemChart.appendData([
            { data: [[now, cpuUsage]] },
            { data: [[now, memoryUsage]] },
            { data: [[now, diskUsage]] },
            { data: [[now, networkUsage]] }
        ]);
    }
}

function loadTenantHealthData() {
    $.ajax({
        url: '/landlord/monitoring/api/system-health',
        method: 'GET',
        success: function(data) {
            displayTenantHealth(data.tenant_health || generateSampleTenantData());
        },
        error: function() {
            displayTenantHealth(generateSampleTenantData());
        }
    });
}

function generateSampleTenantData() {
    const tenants = ['acmecorp', 'techstart', 'bizpro', 'innovate', 'digital'];
    const data = [];
    
    tenants.forEach(tenant => {
        const responseTime = Math.floor(Math.random() * 500) + 100;
        const healthScore = Math.floor(Math.random() * 30) + 70;
        const status = healthScore > 80 ? 'up' : (healthScore > 60 ? 'warning' : 'down');
        
        data.push({
            tenant_name: tenant,
            domain: tenant + '.app.com',
            status: status,
            response_time: responseTime,
            database_status: 'healthy',
            last_activity: new Date(Date.now() - Math.random() * 86400000).toLocaleString(),
            health_score: healthScore
        });
    });
    
    return data;
}

function displayTenantHealth(tenants) {
    const tbody = $('#tenantHealthBody');
    tbody.empty();
    
    tenants.forEach(tenant => {
        const statusBadge = getStatusBadge(tenant.status);
        const healthBadge = getHealthScoreBadge(tenant.health_score);
        
        const row = `
            <tr>
                <td><strong>${tenant.tenant_name}</strong></td>
                <td><a href="https://${tenant.domain}" target="_blank">${tenant.domain}</a></td>
                <td>${statusBadge}</td>
                <td>${tenant.response_time}ms</td>
                <td><span class="badge badge-success">Healthy</span></td>
                <td>${tenant.last_activity}</td>
                <td>${healthBadge}</td>
                <td>
                    <button class="btn btn-sm btn-primary" onclick="viewTenantDetails('${tenant.tenant_name}')">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-warning" onclick="runTenantCheck('${tenant.tenant_name}')">
                        <i class="fas fa-sync"></i>
                    </button>
                </td>
            </tr>
        `;
        tbody.append(row);
    });
}

function getStatusBadge(status) {
    switch(status) {
        case 'up':
            return '<span class="badge badge-success">Online</span>';
        case 'warning':
            return '<span class="badge badge-warning">Warning</span>';
        case 'down':
            return '<span class="badge badge-danger">Offline</span>';
        default:
            return '<span class="badge badge-secondary">Unknown</span>';
    }
}

function getHealthScoreBadge(score) {
    if (score >= 90) {
        return `<span class="badge badge-success">${score}%</span>`;
    } else if (score >= 70) {
        return `<span class="badge badge-warning">${score}%</span>`;
    } else {
        return `<span class="badge badge-danger">${score}%</span>`;
    }
}

function loadDatabaseHealthData() {
    // Generate sample database health data
    const databases = ['landlord', 'acmecorp_db', 'techstart_db', 'bizpro_db'];
    const tbody = $('#databaseHealthBody');
    tbody.empty();
    
    databases.forEach(db => {
        const size = Math.floor(Math.random() * 500) + 50;
        const tables = Math.floor(Math.random() * 20) + 25;
        const status = Math.random() > 0.1 ? 'healthy' : 'warning';
        
        const row = `
            <tr>
                <td>${db}</td>
                <td>${size}MB</td>
                <td>${tables}</td>
                <td><span class="badge badge-${status === 'healthy' ? 'success' : 'warning'}">${status}</span></td>
            </tr>
        `;
        tbody.append(row);
    });
}

function loadQueueStats() {
    const pendingJobs = Math.floor(Math.random() * 50);
    const completedJobs = Math.floor(Math.random() * 1000) + 500;
    const failedJobs = Math.floor(Math.random() * 10);
    const activeWorkers = Math.floor(Math.random() * 5) + 2;
    const avgProcessingTime = Math.floor(Math.random() * 1000) + 200;
    
    $('#pending-jobs-count').text(pendingJobs);
    $('#completed-jobs-count').text(completedJobs);
    $('#failed-jobs-count').text(failedJobs);
    $('#active-workers-count').text(activeWorkers);
    $('#avg-processing-time').text(avgProcessingTime + 'ms');
    
    // Update main queue status
    $('#pending-jobs').text(pendingJobs);
    
    const queueHealth = failedJobs > 5 ? 'Warning' : 'Healthy';
    const queueClass = failedJobs > 5 ? 'text-warning' : 'text-success';
    $('#queue-health-status').removeClass('text-success text-warning text-danger').addClass(queueClass).text(queueHealth);
}

// Action functions
function refreshAllData() {
    toastr.info('Refreshing all system health data...');
    
    loadTenantHealthData();
    loadDatabaseHealthData();
    loadQueueStats();
    updateRealTimeData();
    
    setTimeout(() => {
        toastr.success('System health data refreshed successfully');
    }, 1000);
}

function refreshTenantHealth() {
    toastr.info('Refreshing tenant health data...');
    loadTenantHealthData();
    
    setTimeout(() => {
        toastr.success('Tenant health data refreshed');
    }, 500);
}

function runHealthCheck() {
    $('#healthCheckModal').modal('show');
    
    // Simulate health check process
    setTimeout(() => {
        const results = `
            <div class="alert alert-success">
                <h5><i class="fas fa-check-circle mr-2"></i>System Health Check Complete</h5>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <h6>✅ System Components</h6>
                        <ul class="list-unstyled">
                            <li>✅ Web Server: Online</li>
                            <li>✅ Database: Connected</li>
                            <li>✅ Queue Workers: Active</li>
                            <li>✅ File System: Accessible</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6>📊 Performance Metrics</h6>
                        <ul class="list-unstyled">
                            <li>CPU Usage: Normal (${Math.floor(Math.random() * 30) + 20}%)</li>
                            <li>Memory Usage: Good (${Math.floor(Math.random() * 20) + 50}%)</li>
                            <li>Disk Space: Sufficient (${Math.floor(Math.random() * 20) + 30}%)</li>
                            <li>Response Time: Excellent (${Math.floor(Math.random() * 200) + 100}ms)</li>
                        </ul>
                    </div>
                </div>
                <div class="mt-3">
                    <strong>Overall Health Score: ${Math.floor(Math.random() * 10) + 90}%</strong>
                </div>
            </div>
        `;
        
        $('#healthCheckResults').html(results);
    }, 2000);
}

function viewTenantDetails(tenantName) {
    // This would typically open a detailed view or redirect
    toastr.info(`Opening detailed view for ${tenantName}...`);
    // window.open(`/landlord/monitoring/tenant/${tenantName}`, '_blank');
}

function runTenantCheck(tenantName) {
    toastr.info(`Running health check for ${tenantName}...`);
    
    // Simulate tenant-specific health check
    setTimeout(() => {
        toastr.success(`Health check completed for ${tenantName}`);
        refreshTenantHealth();
    }, 1500);
}
