# Utilities Module Documentation

## Overview

The Utilities module provides a collection of utility functions and helper services used across the platform. It includes common functionality such as data formatting, validation helpers, string manipulation, date/time utilities, and various helper functions that support other modules.

## Architecture

### Module Structure

```
Utilities/
├── Config/              # Module configuration
├── DTOs/                # Data transfer objects
├── Entities/            # Utility entities
├── Http/                # HTTP layer
│   └── Controllers/     # API controllers
├── Providers/           # Service providers
├── Repositories/        # Data access layer
├── Resources/           # API resources
├── Routes/              # API and web routes
├── Services/            # Business logic services
└── database/            # Database migrations
```

## Database Schema

### Core Entities

#### System Settings
- `id` - Primary key
- `key` - Setting key
- `value` - Setting value
- `type` - Value type (string, integer, boolean, json)
- `group` - Setting group
- `description` - Setting description
- `is_public` - Public setting flag
- `created_at`, `updated_at` - Timestamps

#### Activity Logs
- `id` - Primary key
- `user_id` - User who performed action
- `action` - Action performed
- `entity_type` - Entity type
- `entity_id` - Entity ID
- `old_values` - Old values (JSON)
- `new_values` - New values (JSON)
- `ip_address` - IP address
- `user_agent` - User agent
- `created_at` - Timestamp

#### System Logs
- `id` - Primary key
- `level` - Log level (debug, info, warning, error, critical)
- `message` - Log message
- `context` - Log context (JSON)
- `channel` - Log channel
- `created_at` - Timestamp

#### Cache Keys
- `id` - Primary key
- `key` - Cache key
- `description` - Key description
- `ttl` - Time to live (seconds)
- `tags` - Cache tags (JSON)
- `created_at`, `updated_at` - Timestamps

#### Queued Jobs
- `id` - Primary key
- `job_class` - Job class name
- `payload` - Job payload (JSON)
- `attempts` - Number of attempts
- `reserved_at` - Reserved timestamp
- `available_at` - Available timestamp
- `created_at` - Timestamp

## API Endpoints

### System Settings

**List Settings:** `GET /api/tenant/utilities/settings`

**Query Parameters:**
- `group` - Filter by group
- `public` - Filter by public status

**Create Setting:** `POST /api/tenant/utilities/settings`
**Get Setting:** `GET /api/tenant/utilities/settings/{id}`
**Update Setting:** `PUT /api/tenant/utilities/settings/{id}`
**Delete Setting:** `DELETE /api/tenant/utilities/settings/{id}`
**Get Setting Value:** `GET /api/tenant/utilities/settings/key/{key}`

### Activity Logs

**List Activity Logs:** `GET /api/tenant/utilities/activity-logs`

**Query Parameters:**
- `user_id` - Filter by user
- `action` - Filter by action
- `entity_type` - Filter by entity type
- `from` - Start date
- `to` - End date

**Get Activity Log:** `GET /api/tenant/utilities/activity-logs/{id}`
**Clear Old Logs:** `POST /api/tenant/utilities/activity-logs/clear`

### System Logs

**List System Logs:** `GET /api/tenant/utilities/system-logs`

**Query Parameters:**
- `level` - Filter by log level
- `channel` - Filter by channel
- `from` - Start date
- `to` - End date

**Get System Log:** `GET /api/tenant/utilities/system-logs/{id}`
**Clear Old Logs:** `POST /api/tenant/utilities/system-logs/clear`

### Cache Management

**List Cache Keys:** `GET /api/tenant/utilities/cache/keys`
**Clear Cache:** `DELETE /api/tenant/utilities/cache/clear`
**Clear Cache Key:** `DELETE /api/tenant/utilities/cache/keys/{key}`
**Get Cache Stats:** `GET /api/tenant/utilities/cache/stats`

### Queue Management

**List Queued Jobs:** `GET /api/tenant/utilities/queue/jobs`
**Get Queue Stats:** `GET /api/tenant/utilities/queue/stats`
**Retry Failed Job:** `POST /api/tenant/utilities/queue/jobs/{id}/retry`
**Delete Job:** `DELETE /api/tenant/utilities/queue/jobs/{id}`
**Clear Failed Jobs:** `DELETE /api/tenant/utilities/queue/failed`

## Services

### SystemSettingsService
- Settings CRUD operations
- Settings validation
- Default settings management
- Settings caching

### ActivityLogService
- Activity logging
- Log filtering and searching
- Log cleanup
- Activity tracking

### SystemLogService
- System logging
- Log aggregation
- Log cleanup
- Error tracking

### CacheManagementService
- Cache key management
- Cache clearing
- Cache statistics
- Cache optimization

### QueueManagementService
- Queue job monitoring
- Failed job handling
- Queue statistics
- Job retry logic

## Repositories

### SystemSettingsRepository
- Settings data access
- Settings filtering and searching
- Group-based queries
- Public setting queries

### ActivityLogRepository
- Activity log data access
- Activity log filtering and searching
- User-based queries
- Entity-based queries

### SystemLogRepository
- System log data access
- System log filtering and searching
- Level-based queries
- Channel-based queries

### CacheKeyRepository
- Cache key data access
- Cache key filtering
- Tag-based queries

## DTOs

### CreateSettingData
Typed input transfer object for setting creation with validation.

### UpdateSettingData
Typed input transfer object for setting updates with validation.

## Configuration

### Module Configuration

Module configuration in `Config/utilities.php`:

```php
return [
    'settings' => [
        'cache_enabled' => true,
        'cache_ttl' => 3600,
    ],
    'activity_logs' => [
        'retention_days' => 90,
        'auto_cleanup' => true,
    ],
    'system_logs' => {
        'retention_days' => 30,
        'auto_cleanup' => true,
    },
    'cache' => [
        'default_ttl' => 3600,
        'max_keys' => 10000,
    ],
    'queue' => [
        'max_attempts' => 3,
        'retry_delay' => 60,
    ],
];
```

## Setting Groups

- `general` - General settings
- `security` - Security settings
- `notifications` - Notification settings
- `integrations` - Integration settings
- `ui` - User interface settings

## Log Levels

- `debug` - Debug information
- `info` - Informational messages
- `warning` - Warning messages
- `error` - Error conditions
- `critical` - Critical conditions

## Log Channels

- `app` - Application logs
- `security` - Security logs
- `performance` - Performance logs
- `external` - External API logs

## Cache Tags

- `settings` - Settings cache
- `users` - User data cache
- `permissions` - Permission cache
- `menus` - Menu cache

## Utility Helpers

### String Helpers
- Slug generation
- String sanitization
- Truncation
- Case conversion

### Date/Time Helpers
- Date formatting
- Timezone conversion
- Relative time
- Date calculations

### Validation Helpers
- Email validation
- URL validation
- Phone validation
- IP validation

### Formatting Helpers
- Number formatting
- Currency formatting
- Percentage formatting
- File size formatting

## Business Rules

- System settings are cached for performance
- Activity logs track all user actions
- System logs are automatically cleaned up
- Cache keys are tracked for management
- Failed queue jobs can be retried
- Public settings are accessible without authentication

## Permissions

Utilities module permissions follow the pattern: `utilities.{resource}.{action}`

- `utilities.settings.view` - View settings
- `utilities.settings.manage` - Manage settings
- `utilities.activity_logs.view` - View activity logs
- `utilities.activity_logs.delete` - Delete activity logs
- `utilities.system_logs.view` - View system logs
- `utilities.system_logs.delete` - Delete system logs
- `utilities.cache.view` - View cache
- `utilities.cache.manage` - Manage cache
- `utilities.queue.view` - View queue
- `utilities.queue.manage` - Manage queue

## Testing

Module tests in `Tests/`:

```bash
php artisan test modules/Utilities/Tests --testdox
```

Test coverage includes:
- Unit tests for helpers
- Feature tests for API endpoints
- Service tests
- Cache management tests

## Related Documentation

- [Helper Functions Guide](../../backend/documentation/utilities/helpers.md)
- [Cache Configuration](../../backend/documentation/utilities/cache.md)
