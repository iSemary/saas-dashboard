# Monitoring Module Documentation

## Overview

The Monitoring module provides comprehensive system monitoring functionality including performance tracking, error logging, uptime monitoring, resource usage tracking, and alert management. It enables administrators to monitor system health, detect issues early, and maintain optimal performance.

## Architecture

### Module Structure

```
Monitoring/
├── Config/              # Module configuration
├── Http/                # HTTP layer
│   └── Controllers/     # API controllers
├── Providers/           # Service providers
├── Repositories/        # Data access layer
├── Routes/              # API and web routes
├── Services/            # Business logic services
└── module.json          # Module metadata
```

## Database Schema

### Core Entities

#### System Metrics
- `id` - Primary key
- `metric_type` - Metric type (cpu, memory, disk, network)
- `metric_name` - Metric name
- `value` - Metric value
- `unit` - Unit of measurement
- `timestamp` - Measurement timestamp
- `server_id` - Server identifier
- `created_at` - Timestamp

#### Error Logs
- `id` - Primary key
- `level` - Error level (debug, info, notice, warning, error, critical)
- `message` - Error message
- `exception` - Exception class
- `file` - File where error occurred
- `line` - Line number
- `stack_trace` - Stack trace (JSON)
- `user_id` - User ID (if applicable)
- `url` - Request URL
- `method` - HTTP method
- `ip_address` - Client IP
- `created_at` - Timestamp

#### Performance Logs
- `id` - Primary key
- `request_id` - Request ID
- `url` - Request URL
- `method` - HTTP method
- `response_time` - Response time (ms)
- `memory_usage` - Memory usage (bytes)
- `status_code` - HTTP status code
- `user_id` - User ID (if authenticated)
- `created_at` - Timestamp

#### Uptime Checks
- `id` - Primary key
- `name` - Check name
- `endpoint` - Endpoint to check
- `status` - Check status (up, down, degraded)
- `response_time` - Response time (ms)
- `last_checked_at` - Last check timestamp
- `created_at`, `updated_at` - Timestamps

#### Alerts
- `id` - Primary key
- `alert_type` - Alert type (error, performance, uptime, resource)
- `severity` - Severity level (low, medium, high, critical)
- `title` - Alert title
- `description` - Alert description
- `status` - Alert status (open, acknowledged, resolved)
- `acknowledged_by` - User who acknowledged
- `acknowledged_at` - Acknowledgment timestamp
- `resolved_at` - Resolution timestamp
- `created_at`, `updated_at` - Timestamps

## API Endpoints

### System Metrics

**List Metrics:** `GET /api/tenant/monitoring/metrics`

**Query Parameters:**
- `metric_type` - Filter by metric type
- `from` - Start timestamp
- `to` - End timestamp
- `server_id` - Filter by server

**Get Current Metrics:** `GET /api/tenant/monitoring/metrics/current`
**Get Metric History:** `GET /api/tenant/monitoring/metrics/history`

### Error Logs

**List Error Logs:** `GET /api/tenant/monitoring/errors`

**Query Parameters:**
- `level` - Filter by error level
- `from` - Start timestamp
- `to` - End timestamp
- `search` - Search by message

**Get Error Log:** `GET /api/tenant/monitoring/errors/{id}`
**Delete Error Log:** `DELETE /api/tenant/monitoring/errors/{id}`
**Clear Old Logs:** `POST /api/tenant/monitoring/errors/clear`

### Performance Logs

**List Performance Logs:** `GET /api/tenant/monitoring/performance`

**Query Parameters:**
- `from` - Start timestamp
- `to` - End timestamp
- `slow_only` - Show only slow requests

**Get Performance Log:** `GET /api/tenant/monitoring/performance/{id}`
**Get Performance Stats:** `GET /api/tenant/monitoring/performance/stats`

### Uptime Checks

**List Uptime Checks:** `GET /api/tenant/monitoring/uptime`
**Create Uptime Check:** `POST /api/tenant/monitoring/uptime`
**Get Uptime Check:** `GET /api/tenant/monitoring/uptime/{id}`
**Update Uptime Check:** `PUT /api/tenant/monitoring/uptime/{id}`
**Delete Uptime Check:** `DELETE /api/tenant/monitoring/uptime/{id}`
**Run Check Now:** `POST /api/tenant/monitoring/uptime/{id}/check`
**Get Uptime History:** `GET /api/tenant/monitoring/uptime/{id}/history`

### Alerts

**List Alerts:** `GET /api/tenant/monitoring/alerts`

**Query Parameters:**
- `alert_type` - Filter by alert type
- `severity` - Filter by severity
- `status` - Filter by status
- `from` - Start timestamp
- `to` - End timestamp

**Get Alert:** `GET /api/tenant/monitoring/alerts/{id}`
**Acknowledge Alert:** `POST /api/tenant/monitoring/alerts/{id}/acknowledge`
**Resolve Alert:** `POST /api/tenant/monitoring/alerts/{id}/resolve`
**Get Alert Stats:** `GET /api/tenant/monitoring/alerts/stats`

### Dashboard

**Get Dashboard Data:** `GET /api/tenant/monitoring/dashboard`
**Get System Health:** `GET /api/tenant/monitoring/health`

## Services

### MetricsService
- Metric collection
- Metric aggregation
- Metric history queries
- Threshold monitoring

### ErrorLogService
- Error logging
- Error filtering and searching
- Error aggregation
- Log cleanup

### PerformanceService
- Performance tracking
- Slow request detection
- Performance analytics
- Bottleneck identification

### UptimeService
- Uptime check execution
- Status tracking
- Response time monitoring
- Downtime calculation

### AlertService
- Alert generation
- Alert notification
- Alert acknowledgment
- Alert resolution

## Repositories

### MetricsRepository
- Metrics data access
- Time-series queries
- Aggregation queries
- Threshold queries

### ErrorLogRepository
- Error log data access
- Error filtering and searching
- Level-based queries
- User-based queries

### PerformanceRepository
- Performance log data access
- Slow request queries
- URL-based queries
- Time-range queries

### UptimeRepository
- Uptime check data access
- Status history queries
- Response time queries

### AlertRepository
- Alert data access
- Alert filtering and searching
- Status-based queries
- Severity-based queries

## Configuration

### Environment Variables

```env
# Monitoring Configuration
MONITORING_ENABLED=true
MONITORING_METRICS_RETENTION_DAYS=30
MONITORING_ERROR_LOG_RETENTION_DAYS=90
MONITORING_PERFORMANCE_LOG_RETENTION_DAYS=7
MONITORING_ALERT_EMAIL_ENABLED=true
MONITORING_ALERT_EMAIL_TO=admin@example.com
```

### Module Configuration

Module configuration in `Config/monitoring.php`:

```php
return [
    'metrics' => [
        'enabled' => env('MONITORING_ENABLED', true),
        'collection_interval' => 60, // seconds
        'retention_days' => env('MONITORING_METRICS_RETENTION_DAYS', 30),
    ],
    'errors' => [
        'logging_enabled' => true,
        'retention_days' => env('MONITORING_ERROR_LOG_RETENTION_DAYS', 90),
        'log_level' => 'warning',
    ],
    'performance' => {
        'logging_enabled' => true,
        'retention_days' => env('MONITORING_PERFORMANCE_LOG_RETENTION_DAYS', 7),
        'slow_request_threshold' => 1000, // ms
    },
    'uptime' => [
        'check_interval' => 300, // seconds
        'timeout' => 30, // seconds
        'expected_status_codes' => [200, 201, 202],
    ],
    'alerts' => [
        'email_enabled' => env('MONITORING_ALERT_EMAIL_ENABLED', true),
        'email_to' => env('MONITORING_ALERT_EMAIL_TO', 'admin@example.com'),
        'thresholds' => [
            'cpu' => 80, // percentage
            'memory' => 80, // percentage
            'disk' => 90, // percentage
            'response_time' => 2000, // ms
        ],
    ],
];
```

## Metric Types

- `cpu` - CPU usage percentage
- `memory` - Memory usage percentage
- `disk` - Disk usage percentage
- `network` - Network throughput
- `processes` - Process count
- `connections` - Active connections

## Error Levels

- `debug` - Debug information
- `info` - Informational messages
- `notice` - Normal but significant events
- `warning` - Warning messages
- `error` - Error conditions
- `critical` - Critical conditions

## Alert Severity

- `low` - Low severity
- `medium` - Medium severity
- `high` - High severity
- `critical` - Critical severity

## Alert Types

- `error` - Error-based alerts
- `performance` - Performance-based alerts
- `uptime` - Uptime-based alerts
- `resource` - Resource usage alerts

## Business Rules

- Metrics are collected at configured intervals
- Old logs are automatically cleaned up based on retention settings
- Alerts are generated when thresholds are exceeded
- Uptime checks run at configured intervals
- Performance logs track all requests above threshold
- Error logs capture exceptions with stack traces

## Permissions

Monitoring module permissions follow the pattern: `monitoring.{resource}.{action}`

- `monitoring.metrics.view` - View metrics
- `monitoring.errors.view` - View error logs
- `monitoring.errors.delete` - Delete error logs
- `monitoring.performance.view` - View performance logs
- `monitoring.uptime.view` - View uptime checks
- `monitoring.uptime.manage` - Manage uptime checks
- `monitoring.alerts.view` - View alerts
- `monitoring.alerts.acknowledge` - Acknowledge alerts
- `monitoring.alerts.resolve` - Resolve alerts
- `monitoring.dashboard.view` - View dashboard

## Testing

Module tests in `Tests/`:

```bash
php artisan test modules/Monitoring/Tests --testdox
```

Test coverage includes:
- Unit tests for services
- Feature tests for API endpoints
- Alert generation tests
- Uptime check tests

## Related Documentation

- [System Monitoring Guide](../../backend/documentation/monitoring/guide.md)
- [Alert Configuration](../../backend/documentation/monitoring/alerts.md)
