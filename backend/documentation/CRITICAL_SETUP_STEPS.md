# 🚨 Critical Setup Steps - DO NOT FORGET!

This document outlines the **MANDATORY** steps that must be completed for the SaaS application to function properly. These steps are often forgotten and cause login failures.

## 🔑 **PASSPORT INSTALLATION - CRITICAL STEP**

### Why This Step is Critical
- **Error**: "Personal access client not found. Please create one."
- **Impact**: Users cannot log in to the application
- **Root Cause**: Laravel Passport requires OAuth clients to be created for authentication

### Step-by-Step Installation

#### 1. Generate Passport Keys
```bash
# Generate encryption keys (may require sudo due to permissions)
sudo php artisan passport:keys --force
```

#### 2. Install Passport for Landlord Database
```bash
# Create Passport clients for landlord
php artisan tinker --execute="
\$personalAccessClient = \Laravel\Passport\Client::create([
    'name' => 'Personal Access Client',
    'secret' => \Illuminate\Support\Str::random(40),
    'redirect' => 'http://localhost',
    'personal_access_client' => true,
    'password_client' => false,
    'revoked' => false,
]);

\$passwordClient = \Laravel\Passport\Client::create([
    'name' => 'Password Grant Client',
    'secret' => \Illuminate\Support\Str::random(40),
    'redirect' => 'http://localhost',
    'personal_access_client' => false,
    'password_client' => true,
    'revoked' => false,
]);

echo 'Landlord Passport clients created successfully';
"
```

#### 3. Install Passport for Each Tenant Database
```bash
# For each tenant, run:
php artisan tinker --execute="
\$tenant = \Modules\Tenant\Entities\Tenant::find(TENANT_ID);
\$tenant->makeCurrent();

\$personalAccessClient = \Laravel\Passport\Client::create([
    'name' => 'Personal Access Client',
    'secret' => \Illuminate\Support\Str::random(40),
    'redirect' => 'http://localhost',
    'personal_access_client' => true,
    'password_client' => false,
    'revoked' => false,
]);

\$passwordClient = \Laravel\Passport\Client::create([
    'name' => 'Password Grant Client',
    'secret' => \Illuminate\Support\Str::random(40),
    'redirect' => 'http://localhost',
    'personal_access_client' => false,
    'password_client' => true,
    'revoked' => false,
]);

echo 'Tenant Passport clients created successfully';
"
```

### Automated Passport Setup Command

Create a custom Artisan command to automate this process:

```bash
php artisan make:command SetupPassport
```

Then implement the command to:
1. Generate keys
2. Create clients for landlord
3. Create clients for all tenants
4. Verify installation

## 🌐 **DEFAULT LANGUAGE CREATION - CRITICAL STEP**

### Why This Step is Critical
- **Error**: "ErrorException in TranslateHelper::getLanguage()"
- **Impact**: All pages show "Internal Server Error"
- **Root Cause**: Translation system requires at least one language in the database

### Step-by-Step Installation

```bash
php artisan tinker --execute="
\$language = \Modules\Localization\Entities\Language::create([
    'name' => 'English',
    'locale' => 'en',
    'status' => 'active',
    'is_default' => true,
]);
echo 'Default language created successfully';
"
```

## 👥 **ROLES AND PERMISSIONS - CRITICAL STEP**

### Why This Step is Critical
- **Error**: Permission-related errors in tenant context
- **Impact**: Users cannot access protected routes
- **Root Cause**: Tenant databases need basic roles and permissions

### Step-by-Step Installation

```bash
php artisan tinker --execute="
\$tenant = \Modules\Tenant\Entities\Tenant::find(TENANT_ID);
\$tenant->makeCurrent();

// Create basic roles
\$superAdminRole = \Spatie\Permission\Models\Role::create(['name' => 'superadmin', 'guard_name' => 'web']);
\$adminRole = \Spatie\Permission\Models\Role::create(['name' => 'admin', 'guard_name' => 'web']);
\$userRole = \Spatie\Permission\Models\Role::create(['name' => 'user', 'guard_name' => 'web']);

// Create basic permissions
\$permissions = [
    'read.users', 'create.users', 'update.users', 'delete.users',
    'read.brands', 'create.brands', 'update.brands', 'delete.brands',
    'read.dashboard', 'access.dashboard'
];

foreach (\$permissions as \$permission) {
    \Spatie\Permission\Models\Permission::create(['name' => \$permission, 'guard_name' => 'web']);
}

// Assign all permissions to superadmin
\$superAdminRole->givePermissionTo(\Spatie\Permission\Models\Permission::all());

echo 'Roles and permissions created successfully';
"
```

## 📋 **SETUP CHECKLIST**

### Before Going Live
- [ ] Passport keys generated (`sudo php artisan passport:keys --force`)
- [ ] Passport clients created for landlord database
- [ ] Passport clients created for each tenant database
- [ ] Default language created in landlord database
- [ ] Default language created in each tenant database
- [ ] Basic roles and permissions created for each tenant
- [ ] Admin users assigned to superadmin role
- [ ] Cache tables exist in all databases
- [ ] Test login functionality for both landlord and tenant

### Verification Commands
```bash
# Check Passport clients
php artisan tinker --execute="echo 'Landlord clients: ' . \Laravel\Passport\Client::count();"

# Check languages
php artisan tinker --execute="echo 'Languages: ' . \Modules\Localization\Entities\Language::count();"

# Check tenant setup
php artisan tinker --execute="
\$tenant = \Modules\Tenant\Entities\Tenant::find(1);
\$tenant->makeCurrent();
echo 'Tenant clients: ' . \Laravel\Passport\Client::count();
echo 'Tenant languages: ' . \Modules\Localization\Entities\Language::count();
echo 'Tenant roles: ' . \Spatie\Permission\Models\Role::count();
"
```

## 🚨 **COMMON ERRORS AND SOLUTIONS**

### "Personal access client not found"
- **Solution**: Run Passport installation steps above
- **Prevention**: Always include Passport setup in deployment scripts

### "Internal Server Error" on all pages
- **Solution**: Create default language
- **Prevention**: Include language creation in database seeders

### "Permission denied" errors
- **Solution**: Create roles and permissions for tenant
- **Prevention**: Include permission setup in tenant seeding

### "Table 'cache' doesn't exist"
- **Solution**: Run shared migrations for tenant
- **Prevention**: Ensure all migrations are properly categorized

## 📝 **DEPLOYMENT SCRIPT TEMPLATE**

```bash
#!/bin/bash
# deployment-setup.sh

echo "🚀 Starting SaaS Application Setup..."

# 1. Generate Passport keys
echo "🔑 Generating Passport keys..."
sudo php artisan passport:keys --force

# 2. Create default language
echo "🌐 Creating default language..."
php artisan tinker --execute="
\Modules\Localization\Entities\Language::create([
    'name' => 'English',
    'locale' => 'en',
    'status' => 'active',
    'is_default' => true,
]);
echo 'Default language created';
"

# 3. Setup Passport for landlord
echo "🔐 Setting up Passport for landlord..."
php artisan tinker --execute="
\$personalAccessClient = \Laravel\Passport\Client::create([
    'name' => 'Personal Access Client',
    'secret' => \Illuminate\Support\Str::random(40),
    'redirect' => 'http://localhost',
    'personal_access_client' => true,
    'password_client' => false,
    'revoked' => false,
]);
echo 'Landlord Passport setup complete';
"

# 4. Setup Passport for each tenant
echo "🏢 Setting up Passport for tenants..."
php artisan tinker --execute="
\$tenants = \Modules\Tenant\Entities\Tenant::all();
foreach (\$tenants as \$tenant) {
    \$tenant->makeCurrent();
    
    \$personalAccessClient = \Laravel\Passport\Client::create([
        'name' => 'Personal Access Client',
        'secret' => \Illuminate\Support\Str::random(40),
        'redirect' => 'http://localhost',
        'personal_access_client' => true,
        'password_client' => false,
        'revoked' => false,
    ]);
    
    echo 'Tenant ' . \$tenant->name . ' Passport setup complete';
}
"

echo "✅ Setup completed successfully!"
echo "🔗 Test URLs:"
echo "   Landlord: http://landlord.saas.test/login"
echo "   Tenant: http://customer1.saas.test/login"
```

## 🎯 **REMEMBER THIS!**

**Every time you:**
- Set up a new environment
- Create a new tenant
- Reset the database
- Deploy to production

**You MUST run these steps or the application will not work!**

---

*Last updated: October 5, 2025*
*Created by: AI Assistant*
*Purpose: Prevent critical setup steps from being forgotten*
