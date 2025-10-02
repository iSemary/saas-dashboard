# Landlord Monitoring Dashboard Documentation

## Overview

The Landlord Monitoring Dashboard is a comprehensive system health and tenant management solution designed for multi-tenant SaaS applications. It provides real-time monitoring, analytics, error management, and administrative tools for system administrators and developers.

## Table of Contents

1. [Architecture Overview](#architecture-overview)
2. [Module Structure](#module-structure)
3. [Features](#features)
4. [Installation & Setup](#installation--setup)
5. [Usage Guide](#usage-guide)
6. [API Reference](#api-reference)
7. [Customization](#customization)
8. [Troubleshooting](#troubleshooting)

## Architecture Overview

### Design Pattern
The monitoring system follows the established MVC architecture pattern:
```
Controller → Service → Interface → Repository
```

### Module Structure
```
modules/Monitoring/
├── Http/Controllers/
│   └── MonitoringController.php
├── Services/
│   ├── SystemHealthService.php
│   ├── TenantBehaviorService.php
│   ├── ErrorManagementService.php
│   ├── ResourceInsightsService.php
│   ├── AdminToolsService.php
│   └── DeveloperToolsService.php
├── Repositories/
│   ├── SystemHealthInterface.php
│   ├── SystemHealthRepository.php
│   ├── TenantBehaviorInterface.php
│   ├── TenantBehaviorRepository.php
│   ├── ErrorManagementInterface.php
│   ├── ErrorManagementRepository.php
│   ├── ResourceInsightsInterface.php
│   ├── ResourceInsightsRepository.php
│   ├── AdminToolsInterface.php
│   ├── AdminToolsRepository.php
│   ├── DeveloperToolsInterface.php
│   └── DeveloperToolsRepository.php
├── Routes/
│   └── web.php
└── Providers/
    ├── MonitoringServiceProvider.php
    └── RouteServiceProvider.php
```

### Database Integration
The monitoring system integrates with:
- **Landlord Database**: Tenant metadata, system configurations
- **Tenant Databases**: Individual tenant data and metrics
- **System Tables**: Jobs, failed_jobs, sessions, logs

## Features

### 1. System Health Monitoring

#### Tenant Uptime Checker
- **Real-time status monitoring** for all tenant domains
- **Response time tracking** with historical data
- **HTTP status code monitoring** (200, 404, 500, etc.)
- **SSL certificate validation** and expiry alerts
- **Automated health scoring** based on multiple metrics

#### Database Health
- **Database size monitoring** per tenant
- **Table count verification** against expected schema
- **Connection pool monitoring** and optimization alerts
- **Query performance tracking** and slow query detection
- **Backup status verification** with timestamp tracking

#### Queue & Job Statistics
- **Pending jobs monitoring** across all queues
- **Failed job tracking** with error categorization
- **Completed job statistics** and processing times
- **Worker status monitoring** and performance metrics
- **Queue health scoring** with automatic alerts

### 2. Tenant Behavior Analytics

#### Active Sessions Management
- **Real-time session tracking** across all tenants
- **Concurrent user monitoring** with peak usage detection
- **Session duration analytics** and user engagement metrics
- **Geographic distribution** of active users
- **Device and browser analytics** for optimization insights

#### Login Activity Analysis
- **Login pattern recognition** and anomaly detection
- **Failed login attempt monitoring** with security alerts
- **Multi-factor authentication usage** tracking
- **Login source analysis** (web, mobile, API)
- **User retention metrics** and engagement scoring

#### API Usage Monitoring
- **Request volume tracking** per tenant and endpoint
- **Response time monitoring** with performance alerts
- **Error rate analysis** with automatic categorization
- **Rate limiting effectiveness** and abuse detection
- **API version usage** and deprecation planning

#### Feature Usage Statistics
- **Module adoption rates** across tenant base
- **Feature utilization metrics** for product planning
- **User engagement scoring** per feature
- **Usage trend analysis** with growth predictions
- **Feature performance impact** on system resources

### 3. Error Management System

#### Centralized Log Viewer
- **Multi-tenant log aggregation** with filtering capabilities
- **Real-time log streaming** with search functionality
- **Error categorization** and severity classification
- **Log retention policies** with automated cleanup
- **Export capabilities** for external analysis

#### Recurring Error Detection
- **Pattern recognition algorithms** for error clustering
- **Automatic error grouping** by similarity
- **Frequency analysis** with trend identification
- **Root cause analysis** suggestions
- **Resolution tracking** and knowledge base integration

#### Alert Management
- **Configurable alert thresholds** per error type
- **Multi-channel notifications** (email, Slack, SMS)
- **Escalation policies** for critical errors
- **Alert suppression** during maintenance windows
- **Custom alert rules** with conditional logic

### 4. Resource Insights

#### Database Growth Monitoring
- **Historical size tracking** with growth predictions
- **Storage optimization recommendations** 
- **Index usage analysis** and optimization suggestions
- **Query performance correlation** with database size
- **Automated cleanup recommendations** for old data

#### Storage Usage Analytics
- **File system monitoring** per tenant
- **Media storage tracking** with usage patterns
- **Backup storage analysis** and cost optimization
- **CDN usage statistics** and performance metrics
- **Storage quota management** with automatic alerts

#### Rate Limiting Insights
- **API rate limit monitoring** per tenant
- **Abuse detection algorithms** with automatic blocking
- **Rate limit effectiveness analysis** 
- **Performance impact assessment** of rate limiting
- **Custom rate limit recommendations** based on usage patterns

### 5. Administrative Tools

#### Data Consistency Checker
- **Cross-database integrity verification**
- **Orphaned record detection** and cleanup suggestions
- **Foreign key constraint validation**
- **Data synchronization verification** between systems
- **Automated repair suggestions** for common issues

#### System Maintenance Tools
- **Database optimization utilities** with scheduling
- **Cache management** and invalidation tools
- **Log rotation** and cleanup automation
- **Backup verification** and restoration testing
- **Performance tuning recommendations**

### 6. Developer Tools

#### Migration Status Tracking
- **Per-tenant migration status** with detailed reporting
- **Migration failure detection** and rollback capabilities
- **Schema version tracking** across all databases
- **Migration performance monitoring**
- **Automated migration scheduling** and execution

#### Debugging Utilities
- **System configuration viewer** with environment comparison
- **Cache statistics** and performance analysis
- **Queue debugging tools** with job inspection
- **Database query profiling** and optimization suggestions
- **Performance bottleneck identification**

## Installation & Setup

### Prerequisites
- Laravel 10+ with multi-tenant setup
- PHP 8.1+
- MySQL 8.0+
- Redis (for caching and queues)
- Node.js (for frontend assets)

### Installation Steps

1. **Module Registration**
   ```php
   // Add to config/modules.php
   'Monitoring' => [
       'providers' => [
           Modules\Monitoring\Providers\MonitoringServiceProvider::class,
       ],
   ],
   ```

2. **Database Setup**
   ```bash
   # No additional migrations required - uses existing tables
   # Ensure these tables exist:
   # - tenants, jobs, failed_jobs, sessions
   ```

3. **Permission Setup**
   ```php
   // Add monitoring permissions to your permission system
   'read.monitoring' => 'View monitoring dashboard',
   'manage.monitoring' => 'Manage monitoring settings',
   'admin.monitoring' => 'Full monitoring access',
   ```

4. **Environment Configuration**
   ```env
   # Add to .env
   MONITORING_ENABLED=true
   MONITORING_REFRESH_INTERVAL=30
   MONITORING_ALERT_EMAIL=admin@yourapp.com
   ```

5. **Frontend Assets**
   ```bash
   # Ensure ApexCharts is available
   npm install apexcharts
   # Or use CDN (already included in views)
   ```

### Route Integration
The monitoring routes are automatically registered under `/landlord/monitoring/` with appropriate middleware:
- Authentication required (`auth:web`)
- Role-based access (`role:landlord|developer`)
- Two-factor authentication (`2fa`)

## Usage Guide

### Accessing the Dashboard

1. **Main Dashboard**
   - URL: `/landlord/monitoring/`
   - Overview of all monitoring modules
   - Real-time system metrics
   - Quick access to detailed views

2. **System Health**
   - URL: `/landlord/monitoring/system-health`
   - Detailed system performance metrics
   - Tenant uptime status
   - Database and queue health

3. **Tenant Behavior**
   - URL: `/landlord/monitoring/tenant-behavior`
   - User activity analytics
   - Session management
   - API usage statistics

4. **Error Management**
   - URL: `/landlord/monitoring/error-management`
   - Centralized error logs
   - Alert configuration
   - Error trend analysis

5. **Resource Insights**
   - URL: `/landlord/monitoring/resource-insights`
   - Storage usage monitoring
   - Database growth tracking
   - Performance optimization

6. **Admin Tools**
   - URL: `/landlord/monitoring/admin-tools`
   - Data consistency checks
   - System maintenance utilities
   - Automated cleanup tools

7. **Developer Tools**
   - URL: `/landlord/monitoring/developer-tools`
   - Migration status tracking
   - Debugging utilities
   - System diagnostics

### Enhanced Tenant Management

The monitoring system enhances the existing tenant management with:

#### Database Table Counts
- **Visual indicators** showing current vs expected table counts (e.g., 40/45)
- **Color-coded badges**: Green (complete), Yellow (incomplete), Red (error)
- **Real-time updates** every 30 seconds

#### Database Management Actions
- **Re-migrate**: Fresh migration with data reset
- **Seed**: Add sample/default data to tenant database
- **Re-seed**: Fresh seed with data reset
- **Monitoring**: Direct link to tenant-specific monitoring

#### Usage Examples
```javascript
// Re-migrate a tenant database
$('.tenant-remigrate').click(function() {
    let tenantId = $(this).data('tenant-id');
    // Confirmation dialog and AJAX request
});

// View tenant monitoring
window.open('/landlord/monitoring/tenant/' + tenantId, '_blank');
```

## API Reference

### Real-time Data Endpoints

#### System Health API
```http
GET /landlord/monitoring/api/system-health
```
**Response:**
```json
{
  "timestamp": "2025-10-02T10:30:00Z",
  "system_load": {
    "cpu_usage": 45,
    "load_average": {
      "1min": 0.8,
      "5min": 0.6,
      "15min": 0.4
    }
  },
  "memory_usage": {
    "used_mb": 4096,
    "total_mb": 8192,
    "percentage": 50
  },
  "disk_usage": {
    "used_gb": 175,
    "total_gb": 500,
    "percentage": 35
  },
  "active_connections": 25,
  "queue_size": 12
}
```

#### Tenant Behavior API
```http
GET /landlord/monitoring/api/tenant-behavior
```
**Response:**
```json
{
  "timestamp": "2025-10-02T10:30:00Z",
  "active_sessions": 156,
  "current_logins": 23,
  "api_requests_per_minute": 45,
  "error_rate": 2.1
}
```

#### Error Management API
```http
GET /landlord/monitoring/api/errors
```
**Response:**
```json
{
  "timestamp": "2025-10-02T10:30:00Z",
  "errors_per_minute": 3,
  "critical_errors": 1,
  "error_rate": 1.8,
  "recent_errors": [
    {
      "message": "Database connection timeout",
      "level": "error",
      "count": 5,
      "last_occurred": "2025-10-02T10:25:00Z"
    }
  ]
}
```

#### Resource Insights API
```http
GET /landlord/monitoring/api/resources
```
**Response:**
```json
{
  "timestamp": "2025-10-02T10:30:00Z",
  "cpu_usage": 45,
  "memory_usage": 60,
  "disk_usage": 35,
  "network_io": {
    "incoming_mbps": 125,
    "outgoing_mbps": 89
  }
}
```

### Administrative Actions

#### Run Consistency Check
```http
POST /landlord/monitoring/admin-tools/consistency-check
```
**Response:**
```json
{
  "success": true,
  "checks_performed": 15,
  "issues_found": 2,
  "recommendations": [
    "Optimize database indexes",
    "Clean up orphaned records"
  ],
  "execution_time": "2.3s"
}
```

#### Migration Status
```http
GET /landlord/monitoring/api/migration-status
```
**Response:**
```json
{
  "total_tenants": 25,
  "up_to_date": 23,
  "pending_migrations": 2,
  "migration_issues": 0,
  "tenants": [
    {
      "id": 1,
      "name": "acmecorp",
      "migration_status": "up_to_date",
      "last_migration": "2025-10-02T09:15:00Z"
    }
  ]
}
```

### Tenant Database Management

#### Re-migrate Tenant
```http
POST /landlord/tenants/{id}/remigrate
```

#### Seed Tenant Database
```http
POST /landlord/tenants/{id}/seed
```

#### Re-seed Tenant Database
```http
POST /landlord/tenants/{id}/reseed
```

#### Get Database Health
```http
GET /landlord/tenants/{id}/health
```

## Customization

### Adding Custom Metrics

1. **Extend Repository Interface**
   ```php
   interface SystemHealthInterface
   {
       // Existing methods...
       public function getCustomMetric();
   }
   ```

2. **Implement in Repository**
   ```php
   public function getCustomMetric()
   {
       return [
           'metric_name' => 'Custom Value',
           'timestamp' => now(),
       ];
   }
   ```

3. **Update Service**
   ```php
   public function getCustomMetric()
   {
       return $this->repository->getCustomMetric();
   }
   ```

4. **Add to Controller**
   ```php
   public function getCustomData()
   {
       return response()->json($this->systemHealthService->getCustomMetric());
   }
   ```

### Custom Alert Rules

```php
// In your monitoring configuration
'alerts' => [
    'cpu_usage' => [
        'threshold' => 80,
        'duration' => 300, // 5 minutes
        'action' => 'email',
        'recipients' => ['admin@yourapp.com'],
    ],
    'error_rate' => [
        'threshold' => 5, // 5%
        'duration' => 60, // 1 minute
        'action' => 'slack',
        'webhook' => 'https://hooks.slack.com/...',
    ],
];
```

### Custom Dashboard Widgets

```javascript
// Add custom chart to dashboard
function addCustomChart() {
    const options = {
        series: [{
            name: 'Custom Metric',
            data: []
        }],
        chart: {
            type: 'line',
            height: 300
        },
        // ... other options
    };
    
    const chart = new ApexCharts(document.querySelector("#customChart"), options);
    chart.render();
}
```

## Performance Considerations

### Caching Strategy
- **Real-time data**: 10-second cache
- **Historical data**: 5-minute cache
- **Static metrics**: 1-hour cache
- **Database queries**: Optimized with indexes

### Database Optimization
```sql
-- Recommended indexes for monitoring queries
CREATE INDEX idx_tenants_updated_at ON tenants(updated_at);
CREATE INDEX idx_jobs_queue_created_at ON jobs(queue, created_at);
CREATE INDEX idx_failed_jobs_failed_at ON failed_jobs(failed_at);
```

### Memory Management
- **Chart data**: Limited to last 24 hours for real-time charts
- **Log data**: Paginated with 100 records per page
- **Session data**: Automatic cleanup of old sessions

## Security Considerations

### Access Control
- **Role-based permissions** for different monitoring levels
- **IP whitelisting** for sensitive administrative functions
- **Audit logging** for all monitoring actions
- **Rate limiting** on API endpoints

### Data Privacy
- **Sensitive data masking** in logs and displays
- **GDPR compliance** with data retention policies
- **Encrypted storage** for sensitive monitoring data
- **Access logging** for compliance requirements

## Troubleshooting

### Common Issues

#### 1. Charts Not Loading
**Problem**: ApexCharts not rendering
**Solution**:
```javascript
// Check if ApexCharts is loaded
if (typeof ApexCharts === 'undefined') {
    console.error('ApexCharts library not loaded');
    // Load from CDN or local assets
}
```

#### 2. Real-time Updates Not Working
**Problem**: AJAX requests failing
**Solution**:
```javascript
// Check network connectivity and authentication
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
```

#### 3. Database Connection Issues
**Problem**: Tenant database queries failing
**Solution**:
```php
// Verify tenant database configuration
try {
    DB::connection('tenant')->getPdo();
} catch (\Exception $e) {
    Log::error('Tenant database connection failed: ' . $e->getMessage());
}
```

#### 4. Permission Denied Errors
**Problem**: Users cannot access monitoring features
**Solution**:
```php
// Verify user permissions
if (!auth()->user()->hasPermissionTo('read.monitoring')) {
    abort(403, 'Access denied to monitoring dashboard');
}
```

### Performance Issues

#### 1. Slow Dashboard Loading
- **Check database indexes** on frequently queried tables
- **Optimize chart data queries** with appropriate limits
- **Enable caching** for expensive operations
- **Use database query profiling** to identify bottlenecks

#### 2. High Memory Usage
- **Limit chart data points** to reasonable amounts
- **Implement pagination** for large datasets
- **Use lazy loading** for non-critical components
- **Monitor JavaScript memory leaks** in browser

### Debugging Tools

#### 1. Enable Debug Mode
```env
MONITORING_DEBUG=true
APP_DEBUG=true
```

#### 2. Log Monitoring Queries
```php
// Add to monitoring repository
Log::info('Monitoring query executed', [
    'query' => $query,
    'execution_time' => $executionTime,
    'memory_usage' => memory_get_usage(true),
]);
```

#### 3. Browser Console Debugging
```javascript
// Enable verbose logging
window.monitoringDebug = true;

// Log all AJAX requests
$(document).ajaxSend(function(event, xhr, settings) {
    if (window.monitoringDebug) {
        console.log('AJAX Request:', settings.url);
    }
});
```

## Maintenance

### Regular Tasks
- **Database cleanup**: Remove old monitoring data (>30 days)
- **Log rotation**: Archive old log files
- **Performance review**: Analyze slow queries and optimize
- **Security audit**: Review access logs and permissions

### Automated Maintenance
```php
// Add to scheduled tasks
$schedule->command('monitoring:cleanup')->daily();
$schedule->command('monitoring:optimize')->weekly();
$schedule->command('monitoring:backup')->daily();
```

### Monitoring the Monitor
- **Self-health checks**: Monitor the monitoring system itself
- **Alert on monitoring failures**: Ensure monitoring system uptime
- **Backup monitoring data**: Preserve historical metrics
- **Performance benchmarks**: Track monitoring system performance

## Support and Contributing

### Getting Help
- **Documentation**: This comprehensive guide
- **Issue Tracking**: GitHub issues for bug reports
- **Community Support**: Developer forums and chat
- **Professional Support**: Available for enterprise customers

### Contributing
- **Code Standards**: Follow PSR-12 and Laravel conventions
- **Testing**: Include unit and integration tests
- **Documentation**: Update docs for new features
- **Security**: Follow security best practices

---

**Last Updated**: October 2, 2025  
**Version**: 1.0.0  
**Compatibility**: Laravel 10+, PHP 8.1+
