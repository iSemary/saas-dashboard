# SaSS - All in one

# Table of Contents

-   [Installation](#installation)
-   [Modules Requirements](#modules-requirements)
-   [Commands](#commands)
    -   [Sync Missing Language Translations](#)
-   [Databases](#databases)
-   [Running Landlord-Specific Migrations](#running-landlord-specific-migrations)
    -   [Option 1: Direct Migration Commands](#option-1-direct-migration-commands)
    -   [Option 2: Custom Artisan Command](#option-2-custom-artisan-command)
    -   [Seed Role and Permissions (Landlord)](#seed-role-and-permissions-landlord)
    -   [Landlord Tenant Seeder (Landlord)](#landlord-tenant-seeder-landlord)
    -   [Seed Default Landlord User (Landlord)](#seed-default-landlord-user-landlord)
    -   [Seed Modules (Landlord)](#seed-modules-landlord)
    -   [Seed Configurations (Landlord)](#seed-configurations-landlord)
-   [Logo Files](#logo-files)
-   [Modules Stubs](#modules-stubs)
-   [Global Classes](#global-classes)
-   [Global IDs](#global-ids)
-   [Global Functions](#global-functions)
-   [Global Variables](#global-variables)
-   [Model and Data Structure](#model-and-data-structure)
    -   [Geography](#geography)
    -   [Utilities](#utilities)
        -   [Tag](#tag)
        -   [Type](#type)
        -   [Category](#category)
        -   [Industry](#industry)
-   [Name Convention](#name-convention)
-   [File Handler](#file-handler)
-   [Translatable](#translatable)

---

## Installation

php artisan storage:link

---

## Modules Requirements

-   **POS**: Laravel - jQuery - Node.js
-   **Learning**: Laravel - Next.js - Node.js
-   **Surveys**: Laravel - jQuery - Node.js

---

## Commands

### Sync Missing Language Translations

```bash
php artisan translations:translate-missing
```

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

### Seed Configurations (Landlord)

```bash
php artisan db:seed --class=Modules\\Development\\Database\\Seeders\\ConfigurationsSeeder
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

-   `.slug-input`
-   `.snake-input`
-   `.decimal-input`
-   `.open-create-modal`
-   `.open-edit-modal`
-   `.open-details-btn`
-   `.upload-image`
-   `.view-image`
-   `.generate-password-input`

## Global IDs

-   `#table` for datatables inputs
-   `#ckInput` for ck-editor inputs

---

## Global Functions

-   `translate()` OR `@translate`: For translating dashboard items.
-   // TODO `translateModel()` OR `@translateModel()`: For translating model items (e.g., name, description).

-   `configuration()` OR `@configuration`: For fetching configuration values from configurations table.

---

## Global Variables

-   in ViewServiceProvider

---

## Model and Data Structure

### Geography

-   **Countries**
    -   Provinces
        -   Cities
            -   Towns
                -   Streets

### Utilities

#### Tag

-   **Size**
    -   Small
    -   Medium
    -   Large
-   **Color**
    -   Red
    -   Blue
    -   Green
-   **Material**
    -   Cotton
    -   Polyester
    -   Metal

#### Type

-   **Music**
    -   Jazz
    -   Rock
    -   Classical
-   **Strategy**
    -   Real-time
    -   Turn-based
-   **Format**
    -   Digital
    -   Physical

#### Category

-   Books
-   Movies
-   Games

#### Industry

-   Technology
-   Healthcare
-   Education

---

## Name Convention

-   **Permission Name**: Plural
-   **Role Name**: Single
-   **Route**: Plural

## File Handler

In the main class, use `FileHandler`.

```php
use FileHandler;
```

Define the file columns and their info for example:

```php
protected $fileColumns = [
    'icon' => [ // use custom configurations
        'is_encrypted' => false,
        'access_level' => 'public',
        'metadata' => ['width', 'height', 'aspect_ratio'],
    ],
    'thumbnail', // or use without any configurations
];
```

```php
/**
 * Get the icon URL dynamically.
 *
 * @return string
 */
public function getIconAttribute($value)
{
    return $this->getFileUrl($value);
}

/**
 * Set the icon attribute.
 *
 * @param  mixed  $value
 * @return void
 */
public function setIconAttribute($value)
{
    if ($value instanceof \Illuminate\Http\UploadedFile) {
        $media = $this->upload($value, 'icon');
        $this->attributes['icon'] = $media->id;
    } else {
        $this->attributes['icon'] = $value;
    }
}

```

---

## Translatable

This section demonstrates how to use the Translatable trait in a PHP project.
It includes an example of defining translatable columns and managing translations in a datatable.

The `use Translatable;` statement imports the Translatable trait.

The `$translatableColumns` property is an array that specifies which columns are translatable.

The `->editColumn('description', function($row) { ... })` method in the datatable allows you to manage the translation
of the 'description' column using the `TranslateHelper::returnTranslatableEditor` method.


```php
use Translatable;
```

```php
protected $translatableColumns = ['name', 'description'];
```

in datatable table you can manage the translation by this

```php
->editColumn('description', function($row) {
    return TranslateHelper::returnTranslatableEditor($row, 'description');
})
```
