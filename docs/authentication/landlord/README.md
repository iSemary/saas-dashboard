# Landlord Authentication Documentation

## Overview

The landlord authentication system provides centralized authentication and management for the multi-tenant SaaS platform. It handles super admin access, user management, role/permission administration, and system-wide settings.

## Authentication Flow

### Login

**Endpoint:** `POST /api/landlord/auth/login`

**Request Body:**
```json
{
  "email": "admin@example.com",
  "password": "password",
  "subdomain": "landlord"
}
```

**Response (Success):**
```json
{
  "token": "access_token_here",
  "user": {
    "id": 1,
    "name": "Admin User",
    "email": "admin@example.com",
    "roles": ["super_admin"],
    "permissions": ["*"]
  }
}
```

**Response (2FA Required):**
```json
{
  "requires_2fa": true,
  "temp_token": "temporary_token_here"
}
```

### 2FA Verification

**Endpoint:** `POST /api/landlord/auth/2fa/verify`

**Request Body:**
```json
{
  "temp_token": "temporary_token_here",
  "code": "123456",
  "subdomain": "landlord"
}
```

### Password Reset

**Forgot Password:** `POST /api/landlord/auth/forgot-password`
**Reset Password:** `POST /api/landlord/auth/reset-password`

## Protected Endpoints

### User Profile

**Get Current User:** `GET /api/landlord/auth/me`
**Logout:** `POST /api/landlord/auth/logout`

### 2FA Management

**Setup 2FA:** `POST /api/landlord/auth/2fa/setup`
**Confirm 2FA:** `POST /api/landlord/auth/2fa/confirm`
**Disable 2FA:** `POST /api/landlord/auth/2fa/disable`
**Get Recovery Codes:** `GET /api/landlord/auth/2fa/recovery-codes`

## Super Admin Dashboard

### Dashboard Endpoints

**Get Dashboard Data:** `GET /api/landlord/dashboard`
**Get Statistics:** `GET /api/landlord/dashboard/stats`
**Get Recent Activities:** `GET /api/landlord/dashboard/recent-activities`

**Statistics Include:**
- Total users and growth rate
- Total tenants and growth rate
- Total categories and active count
- Total brands and growth rate
- Active module subscriptions
- System load, disk usage, memory usage
- Database status

### Chart Data

**User Growth Chart:** `GET /api/landlord/dashboard/user-chart` (last 30 days)
**Tenant Growth Chart:** `GET /api/landlord/dashboard/tenant-chart` (last 30 days)
**Email Activity Chart:** `GET /api/landlord/dashboard/email-chart` (last 30 days)
**Module Statistics:** `GET /api/landlord/dashboard/module-stats`

## User Management

### User CRUD Operations

**List Users:** `GET /api/landlord/users`

**Query Parameters:**
- `search`: Search by name, email, username
- `role`: Filter by role
- `status`: Filter by status (active/inactive)
- `date_from`: Filter by creation date from
- `date_to`: Filter by creation date to
- `per_page`: Items per page (default: 15)
- `sort_by`: Sort field (default: created_at)
- `sort_order`: Sort order (default: desc)

**Create User:** `POST /api/landlord/users`

**Request Body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "username": "johndoe",
  "password": "password123",
  "status": "active"
}
```

**Get User:** `GET /api/landlord/users/{user}`
**Update User:** `PUT /api/landlord/users/{user}`
**Delete User:** `DELETE /api/landlord/users/{user}`

### User Role Management

**Assign Roles:** `POST /api/landlord/users/{user}/roles`

**Request Body:**
```json
{
  "roles": [1, 2, 3]
}
```

**Remove Roles:** `DELETE /api/landlord/users/{user}/roles`

**Request Body:**
```json
{
  "roles": [1, 2]
}
```

### User Permissions

**Get User Permissions:** `GET /api/landlord/users/{user}/permissions`

**Response:**
```json
{
  "role_permissions": ["users.view", "users.create"],
  "direct_permissions": ["custom.permission"],
  "all_permissions": ["users.view", "users.create", "custom.permission"],
  "user_roles": ["admin", "manager"]
}
```

### User Status

**Toggle User Status:** `POST /api/landlord/users/{user}/toggle-status`

**Get User Statistics:** `GET /api/landlord/users/stats`

## Role & Permission Management

### Role Management

**List Roles:** `GET /api/landlord/roles`

**Create Role:** `POST /api/landlord/roles`

**Request Body:**
```json
{
  "name": "content_manager",
  "guard_name": "web"
}
```

**Get Role:** `GET /api/landlord/roles/{role}`
**Update Role:** `PUT /api/landlord/roles/{role}`
**Delete Role:** `DELETE /api/landlord/roles/{role}`

### Role Permissions

**Assign Permissions to Role:** `POST /api/landlord/roles/{role}/permissions`

**Request Body:**
```json
{
  "permissions": [1, 2, 3, 4]
}
```

### Permission Management

**List Permissions:** `GET /api/landlord/roles/permissions`

**Query Parameters:**
- `search`: Search by permission name
- `group`: Filter by permission group

**Create Permission:** `POST /api/landlord/roles/permissions`

**Request Body:**
```json
{
  "name": "users.create",
  "guard_name": "web"
}
```

**Update Permission:** `PUT /api/landlord/roles/permissions/{permission}`
**Delete Permission:** `DELETE /api/landlord/roles/permissions/{permission}`

**Get Permission Statistics:** `GET /api/landlord/roles/permissions/stats`

## Security Monitoring

### Login Attempts

**List Login Attempts:** `GET /api/landlord/security/login-attempts`
**Get Login Stats:** `GET /api/landlord/security/login-attempts/stats`
**Get Failed Attempts:** `GET /api/landlord/security/login-attempts/failed-attempts`
**Get Recent Activity:** `GET /api/landlord/security/login-attempts/recent-activity`
**Block IP:** `POST /api/landlord/security/login-attempts/block-ip/{ip}`
**Unblock IP:** `POST /api/landlord/security/login-attempts/unblock-ip/{ip}`

### Activity Logs

**List Activity Logs:** `GET /api/landlord/security/activity-logs`
**Get Activity Stats:** `GET /api/landlord/security/activity-logs/stats`
**Get User Activity:** `GET /api/landlord/security/activity-logs/user/{user}`
**Get System Activity:** `GET /api/landlord/security/activity-logs/system`
**Clear Old Logs:** `POST /api/landlord/security/activity-logs/clear-old-logs`

## System Settings

**Get Settings:** `GET /api/landlord/settings`
**Update Settings:** `PUT /api/landlord/settings`

**Get Security Settings:** `GET /api/landlord/settings/security`
**Update Security Settings:** `PUT /api/landlord/settings/security`

**Get System Settings:** `GET /api/landlord/settings/system`
**Update System Settings:** `PUT /api/landlord/settings/system`

## Permission Groups

**Permission Groups API Resource:** Full CRUD via `api/landlord/permission-groups`

## Middleware

All landlord protected routes use:
- `auth:api` - Authentication required
- `landlord_roles` - Landlord role authorization
- `throttle:60,1` - Rate limiting (60 requests per minute)

## Services

### Key Services

- **AuthService**: Handles login, 2FA, password reset
- **SuperAdminService**: Dashboard data and statistics
- **UserManagementService**: User CRUD and role assignment
- **RolePermissionService**: Role and permission management
- **ActivityLogService**: Activity logging
- **LoginAttemptService**: Login attempt tracking

## Controllers

- **AuthApiController**: Authentication endpoints
- **SuperAdminApiController**: Dashboard and statistics
- **UserManagementApiController**: User management
- **RolePermissionApiController**: Role and permission management
- **LoginAttemptApiController**: Security monitoring
- **ActivityLogApiController**: Activity logging
- **SettingsApiController**: System settings

## Database Tables

- `users` - User accounts
- `roles` - Role definitions
- `permissions` - Permission definitions
- `role_has_permissions` - Role-permission relationships
- `model_has_roles` - User-role relationships
- `model_has_permissions` - User-permission relationships
- `activity_logs` - System activity logs
- `login_attempts` - Login attempt tracking
- `settings` - System settings

## Related Documentation

- [User Journey Flow](../../../backend/documentation/cycles/user-journey-flow-diagram.md)
- [Tenant Authentication](../tenant/README.md)
