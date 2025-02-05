# SaaS - All in one

# Table of Contents

-   [Installation](#installation)
-   [Modules Requirements](#modules-requirements)
-   [Commands](#commands)
    -   [Start App Command](#start-app-command)
    -   [Backup App Command](#backup-app-command)
    -   [Sync Missing Language Translations](#sync-missing-language-translations)
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
-   [Global Data Attributes](#global-data-attributes)
-   [Global Functions](#global-functions)
-   [Global Variables](#global-variables)
-   [Global Data Attributes](#global-data-attributes)
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
-   [React Installation](#react-installation)
-   [React App Routes](#react-app-routes)
-   [React Components](#react-components)
-   [Production Commands](#production-commands)

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

### Start app command

It will run the necessary commands to start the app

```bash
php artisan app:start
```

### Backup App Command

Backup the application and save it in the cloud

```bash
php artisan app:backup
```

### Sync Missing Language Translations

It will sync the missed translations by translating it from google translate API

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
php artisan migrate --path=database/migrations/shared --database=landlord
```

### Option 2: Custom Artisan Command

You can simplify the migration process by using a custom Artisan command:

```bash
php artisan landlord:migrate
```

### Seed Role and Permissions (Landlord)

```bash
php artisan db:seed --class=Database\\Seeders\\Landlord\\RolePermissionSeeder
```

### Landlord Tenant Seeder (Landlord)

Creates a landlord row in the tenants table:

```bash
php artisan db:seed --class=Database\\Seeders\\Landlord\\LandlordTenantSeeder
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
`public/assets/shared/images/icons/logo/README.md`

---

## Modules Stubs

# TODO Write this

---

## Global Classes

-   `.slug-input`
-   `.snake-input`
-   `.decimal-input`
-   `.emoji-input`
-   `.open-create-modal`
-   `.open-edit-modal`
-   `.open-details-btn`
-   `.select-row` // used for datatable multiple rows select
-   `.upload-image`
-   `.view-image`
-   `.file-uploader` ex:

    ```html
    <div
        class="file-uploader"
        data-multiple="true"
        data-required="true"
        data-max-file-size="1024"
        data-allowed-files="png,jpg,pdf,xlsx"
        data-label="Drag & Drop Files Here"
        data-button-label="Browse Files"
    ></div>
    ```

-   `.generate-password-input`

---

## Global IDs

-   `#table` for datatables inputs
-   `#ckInput` for ck-editor inputs

---

## Global Data Attributes

-   `data-toggle="tooltip"`
-   `data-selectable="true"` // for datatable multiple select rows

---

## Global Functions

### PHP

-   `translate('key', ['attribute' => 'value'], 'en')` OR `@translate()` OR `__()` For translating dashboard items.
-   // TODO `translateModel()` OR `@translateModel()`: For translating model items (e.g., name, description).

-   `configuration()` OR `@configuration`: For fetching configuration values from configurations table.

-   `render_number()`: // Output: <span title="1,000,000">1m</span>

### JS

-   `translate('home')` OR `t('home')` OR t('unknown_key', { var1: 'test1' })

---

## Global Variables

-   in ViewServiceProvider

---

## Global Data Attributes

-   data-button-listen="select-row"

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

## Prevent git from chmod changes

```bash
git config core.fileMode false
```

---

## React Installation

To install and set up React for your project, follow these steps:

1. **Install Dependencies**:

    ```bash
    npm install
    ```

2. **Run Development Server**:
    ```bash
    npm run dev
    ```

If you encounter any issues, try the following commands to clean the cache and reinstall dependencies:

1. **Clean npm Cache**:

    ```bash
    npm cache clean --force
    ```

2. **Remove `node_modules` and `package-lock.json`**:

    ```bash
    rm -rf node_modules package-lock.json
    ```

3. **Reinstall Dependencies**:

    ```bash
    npm install
    ```

4. **Run Development Server Again**:
    ```bash
    npm run dev
    ```

---

## React App Routes

---

## React Components

---

## Production Commands

```bash
* * * * * cd /var/www/PROJECT_NAME && php artisan schedule:run >> /dev/null 2>&1
```
