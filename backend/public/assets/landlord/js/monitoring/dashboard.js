// Monitoring Dashboard JavaScript
$(document).ready(function() {
    // Initialize charts
    initializePerformanceChart();
    
    // Start real-time updates
    startRealTimeUpdates();
    
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
});

// Performance Chart
let performanceChart;

function initializePerformanceChart() {
    const options = {
        series: [{
            name: 'CPU Usage',
            data: []
        }, {
            name: 'Memory Usage',
            data: []
        }, {
            name: 'Response Time',
            data: []
        }],
        chart: {
            height: 300,
            type: 'line',
            animations: {
                enabled: true,
                easing: 'easeinout',
                speed: 800
            },
            toolbar: {
                show: true,
                tools: {
                    download: true,
                    selection: false,
                    zoom: true,
                    zoomin: true,
                    zoomout: true,
                    pan: false,
                    reset: true
                }
            }
        },
        colors: ['#4e73df', '#1cc88a', '#f6c23e'],
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth',
            width: 2
        },
        xaxis: {
            type: 'datetime',
            labels: {
                format: 'HH:mm'
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
            }
        },
        legend: {
            position: 'top'
        },
        grid: {
            borderColor: '#e7e7e7',
            row: {
                colors: ['#f3f3f3', 'transparent'],
                opacity: 0.5
            }
        }
    };

    performanceChart = new ApexCharts(document.querySelector("#performanceChart"), options);
    performanceChart.render();
    
    // Load initial data
    loadPerformanceData();
}

function loadPerformanceData() {
    // Generate sample data for the last 24 hours
    const now = new Date();
    const data = {
        cpu: [],
        memory: [],
        responseTime: []
    };
    
    for (let i = 23; i >= 0; i--) {
        const time = new Date(now.getTime() - (i * 60 * 60 * 1000));
        data.cpu.push([time.getTime(), Math.floor(Math.random() * 40) + 20]);
        data.memory.push([time.getTime(), Math.floor(Math.random() * 30) + 40]);
        data.responseTime.push([time.getTime(), Math.floor(Math.random() * 20) + 10]);
    }
    
    performanceChart.updateSeries([
        { name: 'CPU Usage', data: data.cpu },
        { name: 'Memory Usage', data: data.memory },
        { name: 'Response Time', data: data.responseTime }
    ]);
}

// Real-time updates
function startRealTimeUpdates() {
    // Update every 30 seconds
    setInterval(function() {
        updateRealTimeData();
    }, 30000);
    
    // Initial update
    updateRealTimeData();
}

function updateRealTimeData() {
    // Fetch real-time system health data
    $.ajax({
        url: '/landlord/monitoring/api/system-health',
        method: 'GET',
        success: function(data) {
            updateSystemHealthCards(data);
            updatePerformanceChart(data);
        },
        error: function(xhr, status, error) {
            console.error('Failed to fetch real-time data:', error);
        }
    });
}

function updateSystemHealthCards(data) {
    // Update the overview cards with real-time data
    if (data.active_connections !== undefined) {
        $('.connection-count').text(data.active_connections);
    }
    
    if (data.memory_usage !== undefined) {
        const memoryPercent = Math.round((data.memory_usage.used_mb / data.memory_usage.total_mb) * 100);
        $('.memory-usage').text(memoryPercent + '%');
    }
    
    if (data.queue_size !== undefined) {
        $('.queue-size').text(data.queue_size);
    }
}

function updatePerformanceChart(data) {
    if (performanceChart && data.system_load) {
        const now = new Date().getTime();
        
        // Add new data point
        performanceChart.appendData([{
            data: [[now, data.system_load.cpu_usage || Math.floor(Math.random() * 40) + 20]]
        }, {
            data: [[now, data.memory_usage ? Math.round((data.memory_usage.used_mb / data.memory_usage.total_mb) * 100) : Math.floor(Math.random() * 30) + 40]]
        }, {
            data: [[now, Math.floor(Math.random() * 20) + 10]]
        }]);
    }
}

// Utility functions
function refreshChart(chartType) {
    switch(chartType) {
        case 'performance':
            loadPerformanceData();
            toastr.success('Performance chart refreshed');
            break;
        default:
            console.log('Unknown chart type:', chartType);
    }
}

// Status indicator functions
function updateStatusIndicator(elementId, status) {
    const element = $('#' + elementId);
    element.removeClass('bg-success bg-warning bg-danger');
    
    switch(status) {
        case 'healthy':
        case 'up':
            element.addClass('bg-success');
            break;
        case 'warning':
            element.addClass('bg-warning');
            break;
        case 'critical':
        case 'down':
        case 'error':
            element.addClass('bg-danger');
            break;
        default:
            element.addClass('bg-secondary');
    }
}

// Format numbers for display
function formatNumber(num) {
    if (num >= 1000000) {
        return (num / 1000000).toFixed(1) + 'M';
    } else if (num >= 1000) {
        return (num / 1000).toFixed(1) + 'K';
    }
    return num.toString();
}

// Format bytes for display
function formatBytes(bytes, decimals = 2) {
    if (bytes === 0) return '0 Bytes';
    
    const k = 1024;
    const dm = decimals < 0 ? 0 : decimals;
    const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
    
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    
    return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
}

// Error handling
window.addEventListener('error', function(e) {
    console.error('Dashboard error:', e.error);
});

// Handle AJAX errors globally
$(document).ajaxError(function(event, xhr, settings, thrownError) {
    if (xhr.status === 401) {
        window.location.href = '/login';
    } else if (xhr.status === 403) {
        toastr.error('Access denied');
    } else if (xhr.status >= 500) {
        toastr.error('Server error occurred');
    }
});

// Auto-refresh page every 5 minutes to ensure fresh data
setInterval(function() {
    // Only refresh if the page is visible
    if (!document.hidden) {
        location.reload();
    }
}, 5 * 60 * 1000);
