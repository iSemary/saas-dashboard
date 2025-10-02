let currentPeriod = '7d';
let charts = {};

$(document).ready(function() {
    initializeAnalytics();
    bindEvents();
    loadAnalyticsData();
    
    // Auto-refresh every 30 seconds
    setInterval(function() {
        loadRealTimeMetrics();
        loadSystemHealth();
    }, 30000);
});

function initializeAnalytics() {
    // Initialize charts
    initializeTransactionTrendsChart();
    initializePaymentMethodChart();
    initializeCurrencyDistributionChart();
}

function bindEvents() {
    // Period selector
    $('.period-btn').on('click', function() {
        $('.period-btn').removeClass('active');
        $(this).addClass('active');
        currentPeriod = $(this).data('period');
        loadAnalyticsData();
    });
}

function loadAnalyticsData() {
    loadOverview();
    loadTransactionTrends();
    loadPaymentMethodPerformance();
    loadCurrencyDistribution();
    loadFailureAnalysis();
    loadSystemHealth();
}

function loadOverview() {
    $.ajax({
        url: '/landlord/payment-analytics/overview',
        method: 'GET',
        data: { period: currentPeriod },
        success: function(response) {
            if (response.success) {
                updateOverviewCards(response.data);
            }
        },
        error: function(xhr) {
            console.error('Failed to load overview:', xhr);
        }
    });
}

function updateOverviewCards(data) {
    $('#total-transactions').text(formatNumber(data.total_transactions));
    $('#success-rate').text(data.success_rate + '%');
    $('#total-volume').text(formatCurrency(data.total_volume));
    $('#net-volume').text(formatCurrency(data.net_volume));
}

function loadTransactionTrends() {
    $.ajax({
        url: '/landlord/payment-analytics/transaction-trends',
        method: 'GET',
        data: { 
            period: currentPeriod,
            group_by: getGroupByForPeriod(currentPeriod)
        },
        success: function(response) {
            if (response.success) {
                updateTransactionTrendsChart(response.data);
            }
        },
        error: function(xhr) {
            console.error('Failed to load transaction trends:', xhr);
        }
    });
}

function loadPaymentMethodPerformance() {
    $.ajax({
        url: '/landlord/payment-analytics/payment-method-performance',
        method: 'GET',
        data: { period: currentPeriod },
        success: function(response) {
            if (response.success) {
                updatePaymentMethodChart(response.data);
            }
        },
        error: function(xhr) {
            console.error('Failed to load payment method performance:', xhr);
        }
    });
}

function loadCurrencyDistribution() {
    $.ajax({
        url: '/landlord/payment-analytics/currency-distribution',
        method: 'GET',
        data: { period: currentPeriod },
        success: function(response) {
            if (response.success) {
                updateCurrencyDistributionChart(response.data);
            }
        },
        error: function(xhr) {
            console.error('Failed to load currency distribution:', xhr);
        }
    });
}

function loadFailureAnalysis() {
    $.ajax({
        url: '/landlord/payment-analytics/failure-analysis',
        method: 'GET',
        data: { period: currentPeriod },
        success: function(response) {
            if (response.success) {
                updateFailureAnalysis(response.data);
            }
        },
        error: function(xhr) {
            console.error('Failed to load failure analysis:', xhr);
        }
    });
}

function loadSystemHealth() {
    $.ajax({
        url: '/landlord/payment-analytics/system-health',
        method: 'GET',
        success: function(response) {
            if (response.success) {
                updateSystemHealth(response.data);
            }
        },
        error: function(xhr) {
            console.error('Failed to load system health:', xhr);
        }
    });
}

function loadRealTimeMetrics() {
    $.ajax({
        url: '/landlord/payment-analytics/real-time-metrics',
        method: 'GET',
        success: function(response) {
            if (response.success) {
                // Update real-time indicators if needed
                console.log('Real-time metrics:', response.data);
            }
        },
        error: function(xhr) {
            console.error('Failed to load real-time metrics:', xhr);
        }
    });
}

function initializeTransactionTrendsChart() {
    const ctx = document.getElementById('transaction-trends-chart').getContext('2d');
    charts.transactionTrends = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Total Transactions',
                data: [],
                borderColor: 'rgb(54, 162, 235)',
                backgroundColor: 'rgba(54, 162, 235, 0.1)',
                tension: 0.1
            }, {
                label: 'Successful Transactions',
                data: [],
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

function initializePaymentMethodChart() {
    const ctx = document.getElementById('payment-method-chart').getContext('2d');
    charts.paymentMethod = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: [],
            datasets: [{
                data: [],
                backgroundColor: [
                    '#FF6384',
                    '#36A2EB',
                    '#FFCE56',
                    '#4BC0C0',
                    '#9966FF',
                    '#FF9F40'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

function initializeCurrencyDistributionChart() {
    const ctx = document.getElementById('currency-distribution-chart').getContext('2d');
    charts.currencyDistribution = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: [],
            datasets: [{
                label: 'Transaction Volume',
                data: [],
                backgroundColor: 'rgba(54, 162, 235, 0.8)',
                borderColor: 'rgb(54, 162, 235)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

function updateTransactionTrendsChart(data) {
    const labels = data.map(item => item.period);
    const totalTransactions = data.map(item => item.total_transactions);
    const successfulTransactions = data.map(item => item.successful_transactions);
    
    charts.transactionTrends.data.labels = labels;
    charts.transactionTrends.data.datasets[0].data = totalTransactions;
    charts.transactionTrends.data.datasets[1].data = successfulTransactions;
    charts.transactionTrends.update();
}

function updatePaymentMethodChart(data) {
    const labels = data.map(item => item.name);
    const volumes = data.map(item => item.total_volume);
    
    charts.paymentMethod.data.labels = labels;
    charts.paymentMethod.data.datasets[0].data = volumes;
    charts.paymentMethod.update();
}

function updateCurrencyDistributionChart(data) {
    const labels = data.map(item => item.code);
    const volumes = data.map(item => item.total_volume);
    
    charts.currencyDistribution.data.labels = labels;
    charts.currencyDistribution.data.datasets[0].data = volumes;
    charts.currencyDistribution.update();
}

function updateFailureAnalysis(data) {
    // Update error codes list
    let errorCodesHtml = '';
    if (data.top_error_codes && data.top_error_codes.length > 0) {
        data.top_error_codes.forEach(function(error) {
            errorCodesHtml += `
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted">${error.error_code || 'Unknown'}</span>
                    <span class="badge bg-danger">${error.failure_count}</span>
                </div>
            `;
        });
    } else {
        errorCodesHtml = '<p class="text-muted">No errors in this period</p>';
    }
    $('#error-codes-list').html(errorCodesHtml);
    
    // Update gateway failures list
    let gatewayFailuresHtml = '';
    if (data.failures_by_gateway && data.failures_by_gateway.length > 0) {
        data.failures_by_gateway.forEach(function(gateway) {
            gatewayFailuresHtml += `
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted">${gateway.name}</span>
                    <span class="badge bg-warning">${gateway.failure_count}</span>
                </div>
            `;
        });
    } else {
        gatewayFailuresHtml = '<p class="text-muted">No gateway failures in this period</p>';
    }
    $('#gateway-failures-list').html(gatewayFailuresHtml);
}

function updateSystemHealth(data) {
    let statusColor = 'success';
    let statusIcon = 'check-circle';
    
    if (data.overall_status === 'critical') {
        statusColor = 'danger';
        statusIcon = 'exclamation-triangle';
    } else if (data.overall_status === 'warning') {
        statusColor = 'warning';
        statusIcon = 'exclamation-circle';
    }
    
    let healthHtml = `
        <div class="text-center mb-3">
            <i class="fas fa-${statusIcon} fa-3x text-${statusColor}"></i>
            <h5 class="mt-2 text-${statusColor}">${data.overall_status.toUpperCase()}</h5>
        </div>
    `;
    
    // Add component statuses
    if (data.components) {
        healthHtml += '<div class="row">';
        Object.keys(data.components).forEach(function(component) {
            const componentData = data.components[component];
            const componentColor = componentData.status === 'healthy' ? 'success' : 
                                 componentData.status === 'warning' ? 'warning' : 'danger';
            
            healthHtml += `
                <div class="col-6 mb-2">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">${component.replace('_', ' ')}</span>
                        <span class="badge bg-${componentColor}">${componentData.status}</span>
                    </div>
                </div>
            `;
        });
        healthHtml += '</div>';
    }
    
    // Add alerts if any
    if (data.alerts && data.alerts.length > 0) {
        healthHtml += '<hr><h6>Active Alerts</h6>';
        data.alerts.forEach(function(alert) {
            const alertColor = alert.severity === 'critical' ? 'danger' : 
                             alert.severity === 'warning' ? 'warning' : 'info';
            healthHtml += `
                <div class="alert alert-${alertColor} alert-sm mb-2">
                    <small>${alert.message}</small>
                </div>
            `;
        });
    }
    
    $('#system-health-status').html(healthHtml);
}

function getGroupByForPeriod(period) {
    switch(period) {
        case '7d':
            return 'day';
        case '30d':
            return 'day';
        case '90d':
            return 'week';
        case '1y':
            return 'month';
        default:
            return 'day';
    }
}

function formatNumber(num) {
    return new Intl.NumberFormat().format(num);
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount);
}
