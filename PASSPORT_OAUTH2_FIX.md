# Passport OAuth2 Setup Fix

## 🚨 Issue Identified

The "Personal access client not found" error occurs when Laravel Passport OAuth2 keys and personal access client are not properly set up during tenant creation.

## ✅ Solution Implemented

### 1. **Passport Setup Seeder Created**
- **File**: `database/seeders/Tenant/PassportSetupSeeder.php`
- **Purpose**: Automatically sets up Passport for each tenant
- **Features**:
  - Checks if Passport keys exist before generating
  - Creates personal access client if missing
  - Handles errors gracefully
  - Provides detailed feedback

### 2. **TenantSetupCommand Enhanced**
- **File**: `app/Console/Commands/TenantSetupCommand.php`
- **Enhancement**: Added `setupPassportForTenant()` method
- **Integration**: Runs Passport setup after migrations and seeding
- **Error Handling**: Continues if Passport setup fails with warning

### 3. **TenantGenerateCommand Enhanced**
- **File**: `app/Console/Commands/TenantGenerateCommand.php`
- **Enhancement**: Added Passport setup as Step 4
- **Process**: 
  1. Create Tenant
  2. Create Customer and Brand
  3. Setup Tenant Database
  4. **Setup Passport for Tenant** ← NEW
  5. Generate Nginx Configuration
  6. Restart Nginx

### 4. **Documentation Updated**
- **README.md**: Updated installation notes
- **TENANT_GENERATE_COMMAND.md**: Added Passport setup to process steps
- **Error Messages**: Updated to reflect "Personal access client not found"

## 🔧 How It Works

### Automatic Setup Process

When you run tenant creation commands, Passport is now automatically configured:

```bash
# This now includes Passport setup automatically
php artisan tenant:generate --name=mycompany --modules=hr,crm

# This also includes Passport setup automatically  
php artisan tenant:setup --tenant=mycompany
```

### Manual Setup (if needed)

If automatic setup fails, you can manually run:

```bash
# For specific tenant
php artisan tenants:artisan "db:seed --class=Database\\Seeders\\Tenant\\PassportSetupSeeder --database=tenant --force" --tenant=2

# Generate keys manually
php artisan tenants:artisan "passport:keys --force" --tenant=2

# Create personal access client manually
php artisan tenants:artisan "passport:client --personal --name='SaaS Dashboard Personal Access Client'" --tenant=2
```

## 🎯 What Gets Created

### Passport Keys
- `storage/oauth-public.key` - Public key for token verification
- `storage/oauth-private.key` - Private key for token signing

### Personal Access Client
- Client record in `oauth_clients` table
- `personal_access_client` = true
- `name` = "SaaS Dashboard Personal Access Client"
- Used for API authentication

## 🚀 Testing the Fix

### 1. Create a New Tenant
```bash
php artisan tenant:generate --name=testpassport --modules=hr,crm
```

### 2. Check Passport Setup
```bash
# Check if keys exist
ls -la storage/oauth-*.key

# Check if personal access client exists
php artisan tenants:artisan "tinker" --tenant=<tenant_id>
# Then in tinker:
# DB::table('oauth_clients')->where('personal_access_client', true)->first();
```

### 3. Test Login
- Go to `http://testpassport.saas.test/login`
- Use credentials: `superadmin@testpassport.local` / `password123`
- Should no longer see "Personal access client not found" error

## 📋 Files Modified

1. **New File**: `database/seeders/Tenant/PassportSetupSeeder.php`
2. **Modified**: `app/Console/Commands/TenantSetupCommand.php`
3. **Modified**: `app/Console/Commands/TenantGenerateCommand.php`
4. **Modified**: `README.md`
5. **Modified**: `documentation/TENANT_GENERATE_COMMAND.md`

## 🔍 Troubleshooting

### If Passport Setup Still Fails

1. **Check Storage Permissions**:
   ```bash
   sudo chown -R www-data:www-data storage/
   sudo chmod -R 775 storage/
   ```

2. **Check Database Connection**:
   ```bash
   php artisan tenants:artisan "migrate:status" --tenant=<tenant_id>
   ```

3. **Manual Key Generation**:
   ```bash
   php artisan tenants:artisan "passport:keys --force" --tenant=<tenant_id>
   ```

4. **Check OAuth Tables**:
   ```bash
   php artisan tenants:artisan "tinker" --tenant=<tenant_id>
   # Then: DB::table('oauth_clients')->get();
   ```

## 🎉 Benefits

- ✅ **Automatic Setup**: No manual Passport configuration needed
- ✅ **Error Prevention**: Prevents "Personal access client not found" errors
- ✅ **Consistent Process**: All tenants get proper Passport setup
- ✅ **Graceful Handling**: Continues if Passport setup fails
- ✅ **Detailed Feedback**: Clear status messages during setup

## 📚 Related Documentation

- [Laravel Passport Documentation](https://laravel.com/docs/passport)
- [Multi-Tenant Architecture](documentation/multi-tenant-architecture.md)
- [Tenant Generation Command](documentation/TENANT_GENERATE_COMMAND.md)

---

**Status**: ✅ **COMPLETE** - Passport OAuth2 setup is now automatically handled during tenant creation and setup processes.
