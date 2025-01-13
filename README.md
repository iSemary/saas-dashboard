# SaSS - All in one 


# Table of Contents

- [Modules Requirements](#modules-requirements)
- [Databases](#databases)
- [Running Landlord-Specific Migrations](#running-landlord-specific-migrations)
  - [Option 1: Direct Migration Commands](#option-1-direct-migration-commands)
  - [Option 2: Custom Artisan Command](#option-2-custom-artisan-command)
  - [Seed Role and Permissions (Landlord)](#seed-role-and-permissions-landlord)
  - [Landlord Tenant Seeder (Landlord)](#landlord-tenant-seeder-landlord)
  - [Seed Default Landlord User (Landlord)](#seed-default-landlord-user-landlord)
  - [Seed Modules (Landlord)](#seed-modules-landlord)
- [Logo Files](#logo-files)
- [Modules Stubs](#modules-stubs)
- [Global Classes](#global-classes)
- [Global IDs](#global-ids)
- [Global Functions](#global-functions)
- [Model and Data Structure](#model-and-data-structure)
  - [Geography](#geography)
  - [Utilities](#utilities)
    - [Tag](#tag)
    - [Type](#type)
    - [Category](#category)
    - [Industry](#industry)
- [Name Convention](#name-convention)

---

## Modules Requirements

- **POS**: Laravel - jQuery - Node.js
- **Learning**: Laravel - Next.js - Node.js
- **Surveys**: Laravel - jQuery - Node.js

---

## Databases

---

## Running Landlord-Specific Migrations

### Option 1: Direct Migration Commands

Run individual migration commands for the landlord database:

```bash
php artisan migrate --path=database/migrations/landlord --database=landlord
php artisan migrate --path=modules/*/Database/Migrations/landlord --database=landlord
php artisan migrate --path=modules/*/Database/migrations/landlord --database=landlord
php artisan migrate --path=modules/*/Database/migrations/shared --database=landlord
php artisan migrate --path=modules/*/Database/Migrations/shared --database=landlord
```

### Option 2: Custom Artisan Command

You can simplify the migration process by using a custom Artisan command:

```bash
php artisan landlord:migrate
```

### Seed Role and Permissions (Landlord)

```bash
php artisan db:seed --class=Database\\Seeders\\landlord\\RolePermissionSeeder
```

### Landlord Tenant Seeder (Landlord)

Creates a landlord row in the tenants table:

```bash
php artisan db:seed --class=Database\\Seeders\\landlord\\LandlordTenantSeeder
```

### Seed Default Landlord User (Landlord)

Creates a user row in the landlord tenant table:

```bash
php artisan db:seed --class=Modules\\Auth\\Database\\Seeders\\LandlordUserSeeder
```

### Seed Modules (Landlord)


```bash
php artisan db:seed --class=Modules\\Utilities\\Database\\Seeders\\ModulesSeeder
```

---

## Logo Files

Please refer to the README file for the file structure: 
`public/assets/global/images/icons/logo/README.md`

---

## Modules Stubs
# TODO Write this
---

## Global Classes

- `.slug-input`
- `.open-create-modal`
- `.open-edit-modal`
- `.open-details-btn`
- `.upload-image`

## Global IDs
- `#table`

---

## Global Functions

- `translate()` OR `@translate`: For translating dashboard items.
- `translateModel()` OR `@translateModel()`: For translating model items (e.g., name, description).

---

## Model and Data Structure

### Geography

- **Countries**
  - Provinces
    - Cities
      - Towns
        - Streets

### Utilities

#### Tag

- **Size**
  - Small
  - Medium
  - Large
- **Color**
  - Red
  - Blue
  - Green
- **Material**
  - Cotton
  - Polyester
  - Metal

#### Type

- **Music**
  - Jazz
  - Rock
  - Classical
- **Strategy**
  - Real-time
  - Turn-based
- **Format**
  - Digital
  - Physical

#### Category

- Books
- Movies
- Games

#### Industry

- Technology
- Healthcare
- Education

---

## Name Convention

- **Permission Name**: Plural
- **Role Name**: Single
- **Route**: Plural
