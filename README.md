# Running Landlord-Specific Migrations

To run migrations for the `landlord` database, you have the following options:

## Option 1: Direct Migration Commands

Run individual migration commands for the landlord database:

```bash
php artisan migrate --path=database/migrations/landlord --database=landlord
php artisan migrate --path=modules/*/Database/Migrations/landlord --database=landlord
php artisan migrate --path=modules/*/Database/migrations/landlord --database=landlord
php artisan migrate --path=modules/*/Database/migrations/shared --database=landlord
php artisan migrate --path=modules/*/Database/Migrations/shared --database=landlord
```

## Option 2: Custom Artisan Command

You can simplify the migration process by using a custom Artisan command:

```bash
php artisan landlord:migrate
```

## Seed Role and Permissions [LandLord]

```bash
php artisan db:seed --class=Database\\Seeders\\landlord\\RolePermissionSeeder

```

## Landlord Tenant [LandLord]

```bash
php artisan db:seed --class=Database\\Seeders\\landlord\\LandlordTenantSeeder

```


## Seed Default landlord user [LandLord]

```bash
php artisan db:seed --class=Modules\\Auth\\Database\\Seeders\\LandlordUserSeeder

```

### Logo Files
Please read this README.md file for files structure public/assets/global/images/icons/logo/README.md


## Modules Stubs



## Global Classes

.slug-input


## Global Functions

translate() OR @translate : for translating dashboard items

translateModel() OR @translateModel() : for translating models items (name, description, etc...)

## Model and Data Structure

### Geography
- **Countries**
  - Provinces
    - Cities
      - Towns
        - Streets

---

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
