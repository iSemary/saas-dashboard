# Tenant Owner Management

## Overview

The Tenant Owner Management system allows landlords to manage users associated with each tenant. This system provides comprehensive functionality for assigning users to tenants, managing roles, permissions, and statuses.

## Features

### Core Functionality

- **User Assignment**: Assign users to specific tenants
- **Role Management**: Define roles (owner, admin, manager, user) for tenant users
- **Super Admin Control**: Promote/demote users to/from super admin status
- **Permission Management**: Set granular permissions for each tenant user
- **Status Management**: Control user status (active, inactive, suspended)
- **Soft Deletes**: Safely remove users from tenants without permanent deletion

### User Interface

- **Tenant Owners Page**: Main management interface accessible from the sidebar
- **View Users Button**: Quick access from tenant pages to view associated users
- **CRUD Operations**: Full create, read, update, delete functionality
- **Search & Filter**: Advanced filtering and search capabilities
- **Statistics Dashboard**: Overview of tenant owner statistics

## Database Schema

### Tenant Owners Table

```sql
CREATE TABLE tenant_owners (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    role VARCHAR(255) DEFAULT 'owner',
    is_super_admin BOOLEAN DEFAULT FALSE,
    permissions JSON NULL,
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    created_by BIGINT UNSIGNED NULL,
    updated_by BIGINT UNSIGNED NULL,
    deleted_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL,
    
    UNIQUE KEY unique_tenant_user (tenant_id, user_id),
    INDEX idx_tenant_role (tenant_id, role),
    INDEX idx_tenant_super_admin (tenant_id, is_super_admin),
    INDEX idx_tenant_status (tenant_id, status)
);
```

## API Endpoints

### Resource Routes

- `GET /landlord/tenant-owners` - List all tenant owners
- `POST /landlord/tenant-owners` - Create new tenant owner
- `GET /landlord/tenant-owners/{id}` - Get specific tenant owner
- `PUT /landlord/tenant-owners/{id}` - Update tenant owner
- `DELETE /landlord/tenant-owners/{id}` - Delete tenant owner

### Additional Routes

- `GET /landlord/tenant-owners/tenant/{tenantId}` - Get tenant owners for specific tenant
- `GET /landlord/tenant-owners/tenant/{tenantId}/super-admins` - Get super admins for tenant
- `GET /landlord/tenant-owners/search?q={query}` - Search tenant owners
- `POST /landlord/tenant-owners/{id}/restore` - Restore soft-deleted tenant owner
- `POST /landlord/tenant-owners/{id}/promote` - Promote user to super admin
- `POST /landlord/tenant-owners/{id}/demote` - Demote user from super admin
- `PUT /landlord/tenant-owners/{id}/status` - Update tenant owner status
- `PUT /landlord/tenant-owners/{id}/permissions` - Update tenant owner permissions
- `GET /landlord/tenant-owners/stats` - Get dashboard statistics

## Usage Examples

### Creating a Tenant Owner

```php
use Modules\Tenant\Services\TenantOwnerService;

$tenantOwnerService = app(TenantOwnerService::class);

$tenantOwner = $tenantOwnerService->create([
    'tenant_id' => 1,
    'user_id' => 5,
    'role' => 'admin',
    'is_super_admin' => false,
    'permissions' => ['read.tenant_owners', 'create.tenant_owners'],
    'status' => 'active',
]);
```

### Assigning User to Tenant

```php
$tenantOwner = $tenantOwnerService->assignUserToTenant(
    $userId = 5,
    $tenantId = 1,
    ['role' => 'manager', 'is_super_admin' => false]
);
```

### Promoting to Super Admin

```php
$promoted = $tenantOwnerService->promoteToSuperAdmin($tenantOwnerId);
```

### Checking User Permissions

```php
$isOwner = $tenantOwnerService->isUserTenantOwner($userId, $tenantId);
$isSuperAdmin = $tenantOwnerService->isUserSuperAdmin($userId, $tenantId);
```

### Getting Tenant Statistics

```php
$stats = $tenantOwnerService->getDashboardStats();
// Returns: total, active, inactive, suspended, super_admins, etc.
```

## Roles and Permissions

### Available Roles

- **Owner**: Full access to tenant management
- **Admin**: Administrative access with most permissions
- **Manager**: Management-level access with limited permissions
- **User**: Basic user access with minimal permissions

### Permission Structure

Permissions are stored as JSON array and can include:

```php
[
    'read.tenant_owners',
    'create.tenant_owners',
    'update.tenant_owners',
    'delete.tenant_owners',
    'restore.tenant_owners',
    // ... other permissions
]
```

### Super Admin Privileges

Super admins have elevated privileges within their tenant:

- Can manage other tenant users
- Can promote/demote other users
- Have access to all tenant resources
- Can modify tenant settings

## Status Management

### Available Statuses

- **Active**: User can access tenant resources
- **Inactive**: User access is temporarily disabled
- **Suspended**: User access is suspended (usually for violations)

### Status Transitions

```php
// Activate user
$tenantOwnerService->activate($tenantOwnerId);

// Deactivate user
$tenantOwnerService->deactivate($tenantOwnerId);

// Suspend user
$tenantOwnerService->suspend($tenantOwnerId);
```

## Validation Rules

### Tenant Owner Form Request

```php
[
    'tenant_id' => 'required|integer|exists:tenants,id',
    'user_id' => 'required|integer|exists:users,id|unique:tenant_owners,user_id,NULL,id,tenant_id,{tenant_id}',
    'role' => 'required|string|in:owner,admin,manager,user',
    'is_super_admin' => 'boolean',
    'permissions' => 'nullable|array',
    'permissions.*' => 'string',
    'status' => 'required|in:active,inactive,suspended',
]
```

## Security Considerations

### Access Control

- Only landlords can access tenant owner management
- Users can only be assigned to existing tenants
- Unique constraint prevents duplicate user-tenant assignments
- Soft deletes preserve data integrity

### Permission Validation

- All operations require appropriate permissions
- Super admin status is carefully managed
- Status changes are logged and auditable

## Integration Points

### With Tenant Management

- "View Users" button on tenant pages
- Tenant statistics include user counts
- Tenant deletion cascades to tenant owners

### With User Management

- User deletion cascades to tenant owner records
- User information is displayed in tenant owner listings
- User search functionality integrated

### With Permission System

- Uses existing permission structure
- Integrates with role-based access control
- Supports granular permission management

## Testing

### Test Coverage

The system includes comprehensive tests covering:

- CRUD operations
- Role and permission management
- Status transitions
- Super admin functionality
- Search and filtering
- Statistics generation
- Relationship validation

### Running Tests

```bash
# Run all tenant owner tests
php artisan test modules/Tenant/Tests/Feature/TenantOwnerTest.php

# Run specific test
php artisan test --filter=it_can_create_a_tenant_owner
```

## Troubleshooting

### Common Issues

1. **Duplicate User Assignment**: Ensure unique constraint is respected
2. **Permission Denied**: Check user has appropriate landlord permissions
3. **Cascade Deletion**: Be aware of cascade effects when deleting tenants/users
4. **Status Conflicts**: Ensure status transitions are valid

### Debugging

```php
// Check if user is tenant owner
$isOwner = $tenantOwnerService->isUserTenantOwner($userId, $tenantId);

// Get user's tenant assignments
$assignments = $tenantOwnerService->getTenantOwnersByUser($userId);

// Check super admin status
$isSuperAdmin = $tenantOwnerService->isUserSuperAdmin($userId, $tenantId);
```

## Future Enhancements

### Planned Features

- Bulk user assignment
- Advanced permission templates
- User activity logging
- Automated role assignment based on user type
- Integration with external user directories
- Advanced reporting and analytics

### Performance Optimizations

- Caching of permission checks
- Optimized queries for large tenant bases
- Background processing for bulk operations
- Index optimization for search functionality

## Support

For technical support or feature requests, please refer to the main documentation or contact the development team.

## Changelog

### Version 1.0.0
- Initial implementation
- Basic CRUD operations
- Role and permission management
- Super admin functionality
- Search and filtering
- Statistics dashboard
- Comprehensive test coverage
