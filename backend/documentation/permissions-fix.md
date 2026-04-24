# Laravel SaaS Dashboard - Permission Issues and Fixes

## Overview

This document outlines the permission issues encountered when setting up the Laravel SaaS Dashboard with nginx virtual hosts and the solutions implemented to resolve them. The project was initially located in `/home/abdelrahman/me/saas-dashboard` but was moved to `/var/www/saas-dashboard` for better security and standard web server practices.

## Issues Encountered

### 1. Initial Permission Denied Errors

**Problem**: When accessing the application through nginx virtual hosts, we encountered `Permission denied` errors in nginx logs.

**Error Messages**:
```
[crit] realpath() "/home/abdelrahman/me/saas-dashboard/public" failed (13: Permission denied)
[crit] stat() "/home/abdelrahman/me/saas-dashboard/public/" failed (13: Permission denied)
```

**Root Cause**: The nginx web server runs as `www-data` user, but the project directory was owned by `abdelrahman` user with restrictive permissions. This was resolved by moving the project to `/var/www/saas-dashboard` which is the standard location for web applications.

### 2. Laravel Application Errors

**Problem**: Laravel application was throwing `ErrorException: Attempt to read property "locale" on null` error.

**Root Cause**: The `TranslateHelper::getLanguage()` method was returning null because no language records existed in the database.

### 3. Storage and Cache Permission Issues

**Problem**: Laravel artisan commands were failing with permission errors when trying to:
- Create storage symlinks
- Write to log files
- Cache configuration files

**Error Messages**:
```
The stream or file "/home/abdelrahman/me/saas-dashboard/storage/logs/laravel.log" could not be opened in append mode: Permission denied
symlink(): Permission denied
```

### 4. AWS S3 Configuration Issues

**Problem**: Application was trying to initialize AWS S3 client with empty credentials, causing fatal errors.

**Error Message**:
```
Missing required client configuration options: region: (string)
```

## Solutions Implemented

### 1. Project Migration to Standard Location

#### Step 1: Move Project to /var/www/
```bash
# Copy project to standard web directory
sudo cp -r /home/abdelrahman/me/saas-dashboard /var/www/
```

#### Step 2: Set Proper Ownership and Permissions
```bash
# Set ownership to www-data for web server access
sudo chown -R www-data:www-data /var/www/saas-dashboard
sudo chmod -R 755 /var/www/saas-dashboard
```

#### Step 3: Update Nginx Virtual Host Configurations
```bash
# Update all nginx configs to point to new location
sudo sed -i 's|/home/abdelrahman/me/saas-dashboard|/var/www/saas-dashboard|g' /etc/nginx/sites-available/landlord.saas.test
sudo sed -i 's|/home/abdelrahman/me/saas-dashboard|/var/www/saas-dashboard|g' /etc/nginx/sites-available/customer1.saas.test
sudo sed -i 's|/home/abdelrahman/me/saas-dashboard|/var/www/saas-dashboard|g' /etc/nginx/sites-available/customer2.saas.test

# Test and reload nginx
sudo nginx -t
sudo systemctl reload nginx
```

### 2. Laravel Application Fixes

#### Step 1: Fix Translation Helper
**File**: `app/Functions/AppFunctions.php`

**Before**:
```php
function translate($key, $attributes = [], $locale = null)
{
    $language = TranslateHelper::getLanguage();
    return app(TranslateHelper::class)->translate($key, $attributes, $language->locale);
}
```

**After**:
```php
function translate($key, $attributes = [], $locale = null)
{
    $language = TranslateHelper::getLanguage();
    $locale = $language ? $language->locale : app()->getLocale();
    return app(TranslateHelper::class)->translate($key, $attributes, $locale);
}
```

#### Step 2: Create Default Language Record
```bash
php artisan tinker --execute="
\Modules\Localization\Entities\Language::create([
    'name' => 'English',
    'locale' => 'en',
    'is_active' => 1,
    'is_default' => 1
]);
echo 'Language created successfully';
"
```

### 3. Storage and Cache Permission Fixes

#### Step 1: Add User to www-data Group
```bash
sudo usermod -a -G www-data abdelrahman
```

#### Step 2: Set Proper Ownership for Development
```bash
# Allow both user and web server access
sudo chown -R abdelrahman:www-data /var/www/saas-dashboard/storage/ /var/www/saas-dashboard/bootstrap/cache/
sudo chmod -R 775 /var/www/saas-dashboard/storage/ /var/www/saas-dashboard/bootstrap/cache/
```

#### Step 3: Create Storage Symlink Manually
```bash
# Create the storage symlink manually since artisan command failed
sudo ln -sf /var/www/saas-dashboard/storage/app/public /var/www/saas-dashboard/public/storage
sudo chown www-data:www-data /var/www/saas-dashboard/public/storage
```

### 4. AWS S3 Configuration Fix

#### Step 1: Modify AWS Service to Handle Missing Credentials
**File**: `modules/FileManager/Services/AWSService.php`

**Before**:
```php
public function __construct()
{
    $this->bucket = env('AWS_BUCKET');
    $this->s3Client = new S3Client([
        'version' => 'latest',
        'region'  => env('AWS_DEFAULT_REGION'),
        'credentials' => [
            'key'    => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
        ],
        'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false)
    ]);
}
```

**After**:
```php
public function __construct()
{
    $this->bucket = env('AWS_BUCKET');

    // Only initialize S3 client if credentials are provided
    if (env('AWS_ACCESS_KEY_ID') && env('AWS_SECRET_ACCESS_KEY') && env('AWS_DEFAULT_REGION')) {
        $this->s3Client = new S3Client([
            'version' => 'latest',
            'region'  => env('AWS_DEFAULT_REGION'),
            'credentials' => [
                'key'    => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false)
        ]);
    }
}
```

#### Step 2: Update Methods to Handle Null S3 Client
```php
public function upload(UploadedFile $file, string $path = '', string $visibility = 'public')
{
    if (!$this->s3Client) {
        return [
            'success' => false,
            'message' => 'AWS S3 not configured',
            'path' => null,
            'url' => null
        ];
    }
    // ... rest of the method
}
```

## Final Permission Structure

### Directory Ownership and Permissions
```
/var/www/                            # 755, root:root
└── saas-dashboard/                  # 755, www-data:www-data
    ├── storage/                     # 775, abdelrahman:www-data
    ├── bootstrap/cache/             # 775, abdelrahman:www-data
    ├── public/                      # 755, www-data:www-data
    │   └── storage -> ../storage/app/public  # www-data:www-data
    └── .env                         # 644, abdelrahman:abdelrahman
```

### User Groups
- `abdelrahman` user added to `www-data` group
- Both user and web server can access necessary directories

## Verification Commands

### Test Application Access
```bash
curl -I http://landlord.saas.test
curl -I http://customer1.saas.test
curl -I http://customer2.saas.test
```

### Test Artisan Commands
```bash
php artisan config:cache
php artisan config:clear
php artisan storage:link
```

### Check Permissions
```bash
ls -la /var/www/saas-dashboard/
ls -la /var/www/saas-dashboard/storage/
ls -la /var/www/saas-dashboard/bootstrap/cache/
```

## Best Practices for Laravel + Nginx Setup

1. **Directory Permissions**: Use 755 for directories, 644 for files
2. **Storage Permissions**: Use 775 for storage and cache directories
3. **User Groups**: Add development user to www-data group
4. **Symlinks**: Create storage symlinks manually if artisan fails
5. **Environment Files**: Keep .env files with proper ownership
6. **Log Files**: Ensure log directories are writable by web server

## Troubleshooting

### Common Permission Issues
1. **403 Forbidden**: Check directory permissions and ownership
2. **500 Internal Server Error**: Check log files and PHP-FPM configuration
3. **Permission Denied on Artisan**: Check storage/cache permissions
4. **Symlink Errors**: Create symlinks manually with proper ownership

### Useful Commands
```bash
# Check nginx error logs
sudo tail -f /var/log/nginx/error.log
sudo tail -f /var/log/nginx/landlord.saas.test.error.log

# Check Laravel logs
tail -f storage/logs/laravel.log

# Check PHP-FPM status
sudo systemctl status php8.4-fpm

# Test nginx configuration
sudo nginx -t
```

## Conclusion

The permission issues were resolved by:
1. **Moving the project to `/var/www/saas-dashboard`** - the standard location for web applications
2. Setting proper directory permissions for nginx access
3. Adding the development user to the www-data group
4. Creating necessary database records for the application
5. Modifying services to handle missing configuration gracefully
6. Manually creating storage symlinks with proper ownership
7. Updating nginx virtual host configurations to point to the new location

The application now runs successfully with all three virtual hosts (landlord.saas.test, customer1.saas.test, customer2.saas.test) working properly from the `/var/www/saas-dashboard` location.
