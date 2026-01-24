# Laravel Passport Setup Guide

This guide explains how to set up Laravel Passport OAuth clients for the multi-tenant SaaS dashboard.

## 🔐 What is Laravel Passport?

Laravel Passport provides a full OAuth2 server implementation for your Laravel application. It's used for API authentication in this project.

## ⚠️ Error: "Personal access client not found"

If you see this error:
```
Personal access client not found. Please create one.
```

It means Passport clients haven't been installed yet. Follow the steps below to fix it.

## 🚀 Quick Fix

Run the Passport clients seeder:

```bash
cd /home/abdelrahman/me/freelance/saas/saas-dashboard
php artisan db:seed --class=Database\\Seeders\\Landlord\\PassportClientsSeeder
```

This will create OAuth clients for:
- ✅ Landlord database
- ✅ All existing tenant databases (customer1, customer2, etc.)

## 📋 Manual Setup

### Option 1: Using Artisan Commands

#### For Landlord Database

```bash
# Switch to landlord connection
php artisan passport:install --force
```

#### For Tenant Databases

For each tenant, you need to run:

```bash
# For customer1
php artisan tenants:artisan "passport:install --force" --tenant=1

# For customer2
php artisan tenants:artisan "passport:install --force" --tenant=2
```

### Option 2: Using the Seeder (Recommended)

The seeder automatically handles all databases:

```bash
php artisan db:seed --class=Database\\Seeders\\Landlord\\PassportClientsSeeder
```

## 🔧 What Gets Created?

The seeder creates:

1. **Personal Access Client** in `oauth_clients` table
2. **Personal Access Client Record** in `oauth_personal_access_clients` table

These are required for:
- API authentication
- Personal access tokens
- User token generation

## 📝 Database Structure

Passport creates these tables (already migrated):

- `oauth_clients` - OAuth client applications
- `oauth_personal_access_clients` - Personal access client configuration
- `oauth_access_tokens` - Access tokens
- `oauth_auth_codes` - Authorization codes
- `oauth_refresh_tokens` - Refresh tokens

## 🧪 Testing

After running the seeder, test login:

1. Go to http://landlord.saas.test/login
2. Login with your credentials
3. The error should be gone!

## 🔄 Adding Clients for New Tenants

When you create a new tenant, run:

```bash
# Install clients for the new tenant
php artisan tenants:artisan "passport:install --force" --tenant={tenant_id}
```

Or simply run the seeder again (it will skip existing clients):

```bash
php artisan db:seed --class=Database\\Seeders\\Landlord\\PassportClientsSeeder
```

## 🛠️ Troubleshooting

### Error: "Connection refused"

Make sure your database connections are configured correctly in `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=landlord_db
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Tenant database connection
TENANT_DB_CONNECTION=mysql
TENANT_DB_HOST=127.0.0.1
TENANT_DB_PORT=3306
TENANT_DB_USERNAME=your_username
TENANT_DB_PASSWORD=your_password
```

### Error: "Table doesn't exist"

Make sure migrations have been run:

```bash
# Run landlord migrations
php artisan landlord:migrate

# Run tenant migrations
php artisan tenants:migrate
```

### Clients Not Working After Seeding

If clients still don't work:

1. Clear config cache:
   ```bash
   php artisan config:clear
   ```

2. Clear application cache:
   ```bash
   php artisan cache:clear
   ```

3. Re-run the seeder:
   ```bash
   php artisan db:seed --class=Database\\Seeders\\Landlord\\PassportClientsSeeder
   ```

## 📚 Related Documentation

- [Laravel Passport Documentation](https://laravel.com/docs/passport)
- [Credentials Guide](./CREDENTIALS.md)
- [Nginx Setup Guide](./NGINX_SETUP.md)

## 🔐 Security Notes

- Personal access clients are for development/testing
- In production, consider using password clients or authorization code flow
- Keep client secrets secure
- Rotate secrets periodically
