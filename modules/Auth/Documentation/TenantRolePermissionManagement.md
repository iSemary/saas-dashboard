# Tenant Role, Permission & User Management System

## Overview

This comprehensive CRUD system provides role-based access control (RBAC) for tenant users. It follows the **Controller → Service → Interface → Repository** architectural pattern and integrates with Spatie Permission package.

---

## Architecture

```
Request → FormRequest (Validation) → Controller → Service → Repository → Model → Database
```

### Layer Responsibilities

- **FormRequest**: Input validation and data sanitization
- **Controller**: HTTP handling, routing, response formatting
- **Service**: Business logic, validation rules, data transformation
- **Repository**: Data access, query building, database operations
- **Model**: Eloquent ORM, relationships, accessors/mutators

---

## Features

### ✅ Role Management
- ✏️ Create, read, update, delete roles
- 🔐 Assign/sync permissions to roles
- 📊 Role statistics and analytics
- 🛡️ Protected system roles (owner, admin)
- 🗑️ Soft delete support with restore

### ✅ Permission Management
- ✏️ CRUD operations for permissions
- 📦 Bulk create permissions for resources
- 🔗 Group permissions by resource
- 🏷️ Permission naming convention: `action.resource`
- 🔍 Filter by action or resource

### ✅ User Management
- 👥 Complete user lifecycle management
- 🔄 Assign/sync roles and permissions to users
- ✅ Bulk activate/deactivate users
- 🗑️ Bulk delete operations
- 🔑 Password reset functionality
- 📧 Send password reset emails
- 📈 User statistics and reporting

---

## API Endpoints

### Roles

#### List Roles
```
GET /tenant/roles
```
**Query Parameters:**
- `search` (string): Search by role name
- `created_from` (date): Filter from date
- `created_to` (date): Filter to date
- `per_page` (integer, default: 15): Pagination limit

**Response:**
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "name": "admin",
        "guard_name": "web",
        "permissions": [],
        "users": [],
        "created_at": "2025-01-01T00:00:00.000000Z"
      }
    ],
    "total": 5
  },
  "statistics": {
    "total_roles": 5,
    "roles_with_users": 3,
    "most_used_role": {}
  }
}
```

#### Create Role
```
POST /tenant/roles
```
**Request Body:**
```json
{
  "name": "manager",
  "guard_name": "web",
  "permissions": [1, 2, 3]
}
```

#### Update Role
```
PUT /tenant/roles/{id}
```

#### Delete Role
```
DELETE /tenant/roles/{id}
```

#### Assign Permissions
```
POST /tenant/roles/{id}/assign-permissions
```
**Request Body:**
```json
{
  "permissions": [1, 2, 3]
}
```

#### Sync Permissions
```
POST /tenant/roles/{id}/sync-permissions
```
**Request Body:**
```json
{
  "permissions": [1, 2, 3]
}
```

#### Role Statistics
```
GET /tenant/roles/statistics/data
```

---

### Permissions

#### List Permissions
```
GET /tenant/permissions
```
**Query Parameters:**
- `search` (string): Search by permission name
- `resource` (string): Filter by resource name
- `action` (string): Filter by action (view, create, update, delete)
- `created_from` (date): Filter from date
- `created_to` (date): Filter to date
- `per_page` (integer, default: 15): Pagination limit

#### Create Permission
```
POST /tenant/permissions
```
**Request Body:**
```json
{
  "name": "view.products",
  "guard_name": "web",
  "roles": [1, 2]
}
```

#### Update Permission
```
PUT /tenant/permissions/{id}
```

#### Delete Permission
```
DELETE /tenant/permissions/{id}
```

#### Bulk Create Permissions
```
POST /tenant/permissions/bulk/create
```
**Request Body:**
```json
{
  "resource": "products",
  "actions": ["view", "create", "update", "delete"]
}
```
**Response:**
```json
{
  "success": true,
  "message": "Resource permissions created successfully",
  "data": [
    {"id": 1, "name": "view.products"},
    {"id": 2, "name": "create.products"},
    {"id": 3, "name": "update.products"},
    {"id": 4, "name": "delete.products"}
  ]
}
```

#### Get Grouped Permissions
```
GET /tenant/permissions/grouped/list
```

#### Permission Statistics
```
GET /tenant/permissions/statistics/data
```

---

### Users

#### List Users
```
GET /tenant/users
```
**Query Parameters:**
- `search` (string): Search by name, email, or username
- `role_id` (integer): Filter by role ID
- `status` (string): Filter by status (active, inactive)
- `country_id` (integer): Filter by country
- `created_from` (date): Filter from date
- `created_to` (date): Filter to date
- `per_page` (integer, default: 15): Pagination limit

#### Create User
```
POST /tenant/users
```
**Request Body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "username": "johndoe",
  "password": "SecurePass123",
  "password_confirmation": "SecurePass123",
  "country_id": 1,
  "language_id": 1,
  "factor_authenticate": false,
  "roles": [1, 2],
  "permissions": [3, 4]
}
```

#### Update User
```
PUT /tenant/users/{id}
```

#### Delete User
```
DELETE /tenant/users/{id}
```

#### Activate User
```
POST /tenant/users/{id}/activate
```

#### Deactivate User
```
POST /tenant/users/{id}/deactivate
```

#### Bulk Activate Users
```
POST /tenant/users/bulk/activate
```
**Request Body:**
```json
{
  "user_ids": [1, 2, 3]
}
```

#### Bulk Deactivate Users
```
POST /tenant/users/bulk/deactivate
```

#### Bulk Delete Users
```
POST /tenant/users/bulk/delete
```

#### Reset Password
```
POST /tenant/users/{id}/reset-password
```
**Request Body:**
```json
{
  "new_password": "NewSecurePass123",
  "new_password_confirmation": "NewSecurePass123"
}
```

#### Send Password Reset Email
```
POST /tenant/users/{id}/send-password-reset
```

#### Assign Roles
```
POST /tenant/users/{id}/assign-roles
```
**Request Body:**
```json
{
  "roles": [1, 2]
}
```

#### Sync Roles
```
POST /tenant/users/{id}/sync-roles
```

#### User Statistics
```
GET /tenant/users/statistics/data
```

---

## Validation Rules

### Role
- `name`: required, string, max:255, unique, lowercase with underscores only
- `guard_name`: string, in:web,api (default: web)
- `permissions`: array, each must exist in permissions table

### Permission
- `name`: required, string, max:255, unique, format: `action.resource`
- `guard_name`: string, in:web,api (default: web)
- `roles`: array, each must exist in roles table

### User
- `name`: required, string, max:255
- `email`: required, email, unique
- `username`: nullable, string, max:64, alphanumeric with underscores, unique
- `password`: required on create, min:8, confirmed
- `country_id`: nullable, exists in countries table
- `language_id`: nullable, exists in languages table
- `factor_authenticate`: boolean
- `roles`: array, each must exist in roles table
- `permissions`: array, each must exist in permissions table

---

## Permission Naming Convention

Permissions follow the format: `{action}.{resource}`

### Actions
- `view`: Read/List operations
- `create`: Create new records
- `update`: Modify existing records
- `delete`: Remove records

### Examples
```
view.users
create.products
update.orders
delete.categories
manage.roles
assign.permissions
```

---

## Default Roles & Permissions

### Web Roles (Tenant Dashboard)

#### Owner
- **Permissions**: ALL
- **Description**: Full system access

#### Admin
- **Permissions**: Most permissions except deleting roles/permissions
- **Description**: Administrative access

#### Manager
- **Permissions**: view.*, create.*, update.* (except roles/permissions)
- **Description**: Management level access

#### Employee
- **Permissions**: view.*, create.* (limited resources)
- **Description**: Basic employee access

#### Viewer
- **Permissions**: view.* (limited)
- **Description**: Read-only access

---

## Service Methods

### TenantRoleService

```php
// Get all roles with filters
$roles = $roleService->getAllRoles($filters, $perPage);

// Get role by ID
$role = $roleService->getRoleById($id);

// Create role
$result = $roleService->createRole($data);

// Update role
$result = $roleService->updateRole($id, $data);

// Delete role
$result = $roleService->deleteRole($id);

// Assign permissions
$result = $roleService->assignPermissionsToRole($roleId, $permissionIds);

// Sync permissions
$result = $roleService->syncPermissionsForRole($roleId, $permissionIds);

// Get statistics
$stats = $roleService->getRoleStatistics();
```

### TenantPermissionService

```php
// Get all permissions
$permissions = $permissionService->getAllPermissions($filters, $perPage);

// Bulk create for resource
$result = $permissionService->bulkCreateResourcePermissions($resource, $actions);

// Get grouped by resource
$grouped = $permissionService->getPermissionsGroupedByResource();
```

### TenantUserManagementService

```php
// Get all users
$users = $userService->getAllUsers($filters, $perPage);

// Create user
$result = $userService->createUser($data);

// Bulk operations
$result = $userService->bulkActivateUsers($userIds);
$result = $userService->bulkDeactivateUsers($userIds);
$result = $userService->bulkDeleteUsers($userIds);

// Assign/Sync roles
$result = $userService->assignRolesToUser($userId, $roleIds);
$result = $userService->syncRolesForUser($userId, $roleIds);

// Password management
$result = $userService->resetUserPassword($userId, $newPassword);
$result = $userService->sendPasswordResetEmail($userId);
```

---

## Usage Examples

### Creating a New Role with Permissions

```php
use Modules\Auth\Services\Tenant\TenantRoleServiceInterface;

class ExampleController
{
    protected $roleService;

    public function __construct(TenantRoleServiceInterface $roleService)
    {
        $this->roleService = $roleService;
    }

    public function createManagerRole()
    {
        $result = $this->roleService->createRole([
            'name' => 'project_manager',
            'guard_name' => 'web',
            'permissions' => [1, 2, 3, 4, 5]
        ]);

        if ($result['success']) {
            return response()->json([
                'message' => $result['message'],
                'role' => $result['data']
            ]);
        }

        return response()->json([
            'message' => $result['message'],
            'errors' => $result['errors'] ?? []
        ], 400);
    }
}
```

### Bulk Creating Permissions

```php
$result = $permissionService->bulkCreateResourcePermissions('products', [
    'view',
    'create',
    'update',
    'delete'
]);

// Creates:
// - view.products
// - create.products
// - update.products
// - delete.products
```

### Creating User with Role

```php
$result = $userService->createUser([
    'name' => 'Jane Smith',
    'email' => 'jane@example.com',
    'password' => 'SecurePassword123',
    'roles' => ['manager'], // Can use role names or IDs
    'permissions' => [10, 11] // Direct permissions
]);
```

---

## Error Handling

All service methods return standardized arrays:

### Success Response
```php
[
    'success' => true,
    'message' => 'Operation completed successfully',
    'data' => [...] // Result data
]
```

### Error Response
```php
[
    'success' => false,
    'message' => 'Error message',
    'errors' => [...] // Validation errors if applicable
]
```

---

## Security Considerations

1. **Protected Roles**: System roles (owner, admin, super_admin) cannot be deleted
2. **Self-Deletion Prevention**: Users cannot delete themselves
3. **Last Admin Protection**: Cannot delete the last admin user
4. **Permission Validation**: All role/permission assignments are validated
5. **Transaction Safety**: Critical operations use database transactions
6. **Guard Separation**: API and Web guards are strictly separated

---

## Database Seeding

Run the tenant seeder to populate default roles and permissions:

```bash
php artisan db:seed --class=Database\\Seeders\\Tenant\\RolePermissionSeeder
```

This creates:
- 5 Web roles (owner, admin, manager, employee, viewer)
- 4 API roles (owner, super_admin, admin, viewer)
- All resource permissions for configured resources
- Additional management permissions

---

## Testing

Unit tests and integration tests are provided for all components.

Run tests:
```bash
php artisan test --filter TenantRoleTest
php artisan test --filter TenantPermissionTest
php artisan test --filter TenantUserManagementTest
```

---

## Troubleshooting

### Common Issues

**Permission denied errors:**
- Verify user has correct role assignments
- Check guard_name matches (web vs api)
- Ensure permissions are synced to roles

**Unique constraint violations:**
- Role/permission names must be unique per guard
- Check for existing soft-deleted records

**Cannot delete role/user:**
- Review canDeleteRole() and canDeleteUser() logic
- Check for existing dependencies

---

## Support

For issues or questions, refer to:
- Laravel Documentation: https://laravel.com/docs
- Spatie Permission: https://spatie.be/docs/laravel-permission
- Project Repository Issues

---

**Version**: 1.0.0  
**Last Updated**: 2025-01-03  
**Maintainer**: Development Team

