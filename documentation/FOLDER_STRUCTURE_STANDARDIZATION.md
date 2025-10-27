# Folder Structure Standardization

## Overview

This document defines the standardized folder structure for the multi-tenant SaaS application based on Laravel Modules package conventions and Laravel best practices.

## Current Issues Identified

### Inconsistent Folder Naming
The current project has inconsistent folder naming across modules:

1. **Database folder**: Some modules use `Database` (capital D), others use `database` (lowercase)
2. **Migrations folder**: Some use `Migrations` (capital M), others use `migrations` (lowercase)  
3. **Seeders folder**: Some use `Seeders` (capital S), others use `seeders` (lowercase)

### Examples of Current Inconsistencies

**Modules with `Database` (Capital D):**
- `modules/Auth/Database/Migrations/`
- `modules/Auth/Database/Seeders/`
- `modules/Customer/Database/Migrations/`
- `modules/Customer/Database/Seeders/`
- `modules/Tenant/Database/Migrations/`
- `modules/Tenant/Database/Seeders/`

**Modules with `database` (Lowercase d):**
- `modules/Accounting/Database/migrations/`
- `modules/API/Database/migrations/`
- `modules/Comment/Database/migrations/`
- `modules/Comment/Database/seeders/`
- `modules/CRM/Database/migrations/`
- `modules/Development/Database/migrations/`
- `modules/Email/Database/migrations/`
- `modules/FileManager/Database/migrations/`
- `modules/Geography/Database/migrations/`
- `modules/HR/Database/migrations/`
- `modules/Inventory/Database/migrations/`
- `modules/Localization/Database/migrations/`
- `modules/Monitoring/Database/migrations/`
- `modules/Notification/Database/migrations/`
- `modules/Payment/Database/migrations/`
- `modules/Reporting/Database/migrations/`
- `modules/Sales/Database/migrations/`
- `modules/StaticPages/Database/migrations/`
- `modules/Subscription/Database/migrations/`
- `modules/Ticket/Database/migrations/`
- `modules/Utilities/Database/migrations/`
- `modules/Workflow/Database/migrations/`

## Standardized Structure (Based on Laravel Modules Package)

According to the `nwidart/laravel-modules` package configuration (`config/modules.php`), the standardized structure should be:

### Module Structure
```
modules/
├── ModuleName/
│   ├── Config/
│   ├── Database/
│   │   ├── factories/
│   │   ├── migrations/
│   │   └── Seeders/
│   ├── Http/
│   │   ├── Controllers/
│   │   ├── Middleware/
│   │   └── Requests/
│   ├── Providers/
│   ├── Repositories/
│   ├── Resources/
│   │   └── views/
│   ├── Routes/
│   ├── Services/
│   └── Tests/
```

### Key Standardization Rules

1. **Database folder**: Always `Database` (capital D)
2. **Migrations folder**: Always `migrations` (lowercase m)
3. **Seeders folder**: Always `Seeders` (capital S)
4. **All other folders**: Follow Laravel conventions (capital first letter)

### Multi-Tenant Specific Structure

For multi-tenant applications, we need to organize migrations and seeders by database type:

```
modules/ModuleName/
├── Database/
│   ├── migrations/
│   │   ├── landlord/          # Landlord database migrations
│   │   ├── tenant/            # Tenant database migrations  
│   │   └── shared/            # Shared migrations (both databases)
│   └── Seeders/
│       ├── Landlord/          # Landlord database seeders
│       ├── Tenant/            # Tenant database seeders
│       └── Shared/            # Shared seeders (both databases)
```

### Main Database Folder Structure

```
database/
├── migrations/
│   ├── landlord/              # Landlord-specific migrations
│   ├── tenant/                # Tenant-specific migrations
│   └── shared/                # Shared migrations
└── seeders/
    ├── Landlord/              # Landlord-specific seeders
    ├── Tenant/                # Tenant-specific seeders
    └── Shared/                # Shared seeders
```

## Migration Plan

### Phase 1: Rename Inconsistent Folders
1. Rename all `database` folders to `Database` (capital D)
2. Rename all `Migrations` folders to `migrations` (lowercase m)
3. Rename all `seeders` folders to `Seeders` (capital S)

### Phase 2: Organize by Database Type
1. Create subfolders for `landlord`, `tenant`, and `shared` in migrations
2. Create subfolders for `Landlord`, `Tenant`, and `Shared` in Seeders
3. Move existing files to appropriate subfolders

### Phase 3: Update Commands
1. Update `LandlordSetupCommand` to use standardized paths
2. Update `TenantSetupCommand` to use standardized paths
3. Test all commands with new structure

## Benefits of Standardization

1. **Consistency**: All modules follow the same structure
2. **Maintainability**: Easier to locate and manage files
3. **Laravel Compliance**: Follows Laravel and Laravel Modules conventions
4. **Multi-Tenant Clarity**: Clear separation between landlord, tenant, and shared resources
5. **Developer Experience**: Predictable structure for new developers

## Implementation Commands

### Rename Folders Script
```bash
# Rename database folders
find modules -name "database" -type d -exec sh -c 'mv "$1" "$(dirname "$1")/Database"' _ {} \;

# Rename Migrations folders  
find modules -name "Migrations" -type d -exec sh -c 'mv "$1" "$(dirname "$1")/migrations"' _ {} \;

# Rename seeders folders
find modules -name "seeders" -type d -exec sh -c 'mv "$1" "$(dirname "$1")/Seeders"' _ {} \;
```

### Create Subfolder Structure
```bash
# Create landlord/tenant/shared subfolders for migrations
for module in modules/*/Database/migrations; do
    mkdir -p "$module/landlord"
    mkdir -p "$module/tenant" 
    mkdir -p "$module/shared"
done

# Create Landlord/Tenant/Shared subfolders for Seeders
for module in modules/*/Database/Seeders; do
    mkdir -p "$module/Landlord"
    mkdir -p "$module/Tenant"
    mkdir -p "$module/Shared"
done
```

## Verification

After implementation, verify the structure with:
```bash
find modules -type d -name "*atabase*" -o -name "*igration*" -o -name "*eeder*" | sort
```

Expected output should show consistent naming across all modules.
