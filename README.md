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