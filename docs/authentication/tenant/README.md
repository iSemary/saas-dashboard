# Tenant Authentication Documentation

## Overview

The tenant authentication system provides authentication and user management for individual tenants in the multi-tenant SaaS platform. Each tenant has its own isolated database and user management, scoped to that tenant's organization.

## Authentication Flow

### Login

**Endpoint:** `POST /api/tenant/auth/login`

**Request Body:**
```json
{
  "email": "user@tenant.com",
  "password": "password",
  "subdomain": "tenant-subdomain"
}
```

**Response (Success):**
```json
{
  "token": "access_token_here",
  "user": {
    "id": 1,
    "name": "User Name",
    "email": "user@tenant.com",
    "roles": ["admin"],
    "permissions": ["users.view", "users.create"]
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

**Endpoint:** `POST /api/tenant/auth/2fa/verify`

**Request Body:**
```json
{
  "temp_token": "temporary_token_here",
  "code": "123456",
  "subdomain": "tenant-subdomain"
}
```

### Password Reset

**Forgot Password:** `POST /api/tenant/auth/forgot-password`
**Reset Password:** `POST /api/tenant/auth/reset-password`

## Protected Endpoints

### User Profile

**Get Current User:** `GET /api/tenant/auth/me`
**Logout:** `POST /api/tenant/auth/logout`

### 2FA Management

**Setup 2FA:** `POST /api/tenant/auth/2fa/setup`
**Confirm 2FA:** `POST /api/tenant/auth/2fa/confirm`
**Disable 2FA:** `POST /api/tenant/auth/2fa/disable`
**Get Recovery Codes:** `GET /api/tenant/auth/2fa/recovery-codes`

## User Management

### User CRUD Operations

**List Users:** `GET /api/tenant/users`

**Query Parameters:**
- `search`: Search by name, email, username
- `per_page`: Items per page (default: 50)

**Create User:** `POST /api/tenant/users`

**Request Body:**
```json
{
  "name": "John Doe",
  "email": "john@tenant.com",
  "username": "johndoe",
  "password": "password123",
  "status": "active"
}
```

**Get User:** `GET /api/tenant/users/{id}`
**Update User:** `PUT /api/tenant/users/{id}`
**Delete User:** `DELETE /api/tenant/users/{id}`

### User Role Management

**Assign Roles:** `POST /api/tenant/users/{id}/roles`

**Request Body:**
```json
{
  "role_ids": [1, 2, 3]
}
```

## Role Management

### Role CRUD Operations

**List Roles:** `GET /api/tenant/roles`

**Query Parameters:**
- `page`: Page number
- `per_page`: Items per page
- `search`: Search by role name
- `sort_by`: Sort field
- `sort_order`: Sort direction

**Create Role:** `POST /api/tenant/roles`

**Request Body:**
```json
{
  "name": "sales_manager",
  "guard_name": "web"
}
```

**Get Role:** `GET /api/tenant/roles/{id}`
**Update Role:** `PUT /api/tenant/roles/{id}`
**Delete Role:** `DELETE /api/tenant/roles/{id}`

## Permission Management

### Permission CRUD Operations

**List Permissions:** `GET /api/tenant/permissions`

**Create Permission:** `POST /api/tenant/permissions`

**Request Body:**
```json
{
  "name": "deals.create",
  "guard_name": "web"
}
```

**Update Permission:** `PUT /api/tenant/permissions/{id}`
**Delete Permission:** `DELETE /api/tenant/permissions/{id}`

## Profile Management

### User Profile

**Get Profile:** `GET /api/tenant/profile`

**Response:**
```json
{
  "id": 1,
  "name": "User Name",
  "email": "user@tenant.com",
  "username": "username",
  "avatar": "avatar_url",
  "roles": ["admin"],
  "permissions": ["users.view", "users.create"],
  "last_login_at": "2024-01-01T00:00:00Z",
  "created_at": "2024-01-01T00:00:00Z",
  "updated_at": "2024-01-01T00:00:00Z"
}
```

**Update Profile:** `PUT /api/tenant/profile`

**Request Body:**
```json
{
  "name": "Updated Name",
  "email": "updated@tenant.com",
  "username": "updatedusername"
}
```

### Avatar Management

**Upload Avatar:** `POST /api/tenant/profile/avatar`

**Request:** Multipart form data with `avatar` file

**Response:**
```json
{
  "avatar_url": "https://example.com/avatars/user1.jpg"
}
```

### Password Management

**Change Password:** `POST /api/tenant/profile/password`

**Request Body:**
```json
{
  "current_password": "oldpassword",
  "new_password": "newpassword123"
}
```

## Two-Factor Authentication

### 2FA Setup

**Setup 2FA:** `POST /api/tenant/2fa/setup`

**Response:**
```json
{
  "qr_code_url": "otpauth://totp/...",
  "secret": "secret_key_here"
}
```

**Confirm 2FA:** `POST /api/tenant/2fa/confirm`

**Request Body:**
```json
{
  "code": "123456"
}
```

**Disable 2FA:** `POST /api/tenant/2fa/disable`

## Settings Management

**Get Settings:** `GET /api/tenant/settings`
**Update Settings:** `PUT /api/tenant/settings`

## Security Monitoring

### Activity Logs

**List Activity Logs:** `GET /api/tenant/activity-logs`

### Login Attempts

**List Login Attempts:** `GET /api/tenant/login-attempts`

## Middleware

All tenant protected routes use:
- `auth:api` - Authentication required
- `tenant_roles` - Tenant role authorization
- `throttle:60,1` - Rate limiting (60 requests per minute)

## Services

### Key Services

- **AuthService**: Handles login, 2FA, password reset, profile management
- **TenantUserApiService**: Tenant user CRUD and role assignment
- **TenantRoleApiService**: Tenant role management
- **TenantTwoFactorApiService**: Tenant 2FA management

## Controllers

- **AuthApiController**: Authentication endpoints (shared with landlord)
- **TenantUserApiController**: User management
- **TenantRoleApiController**: Role management
- **TenantPermissionApiController**: Permission management
- **TenantProfileApiController**: Profile management
- **TenantTwoFactorApiController**: 2FA management
- **TenantActivityLogApiController**: Activity logging
- **TenantLoginAttemptApiController**: Login attempt tracking
- **TenantSettingsApiController**: Settings management

## Database Tables (Tenant-Scoped)

Each tenant has its own database with these tables:
- `users` - User accounts (tenant-scoped)
- `roles` - Role definitions (tenant-scoped)
- `permissions` - Permission definitions (tenant-scoped)
- `role_has_permissions` - Role-permission relationships
- `model_has_roles` - User-role relationships
- `model_has_permissions` - User-permission relationships
- `activity_logs` - Activity logging
- `login_attempts` - Login attempt tracking
- `settings` - Tenant settings
- `user_meta` - User-specific settings (e.g., animations)

## Tenant Isolation

Each tenant operates in complete isolation:
- Separate database schema
- Separate user accounts
- Separate roles and permissions
- Separate settings and configurations
- Data cannot leak between tenants

## Subdomain Routing

Authentication is subdomain-aware:
- `landlord.example.com` - Landlord authentication
- `tenant1.example.com` - Tenant 1 authentication
- `tenant2.example.com` - Tenant 2 authentication

The `subdomain` parameter in login requests identifies which tenant's database to use.

## Related Documentation

- [User Journey Flow](../../../backend/documentation/cycles/user-journey-flow-diagram.md)
- [Landlord Authentication](../landlord/README.md)
