# Environment Setup Documentation

## Overview

This document provides comprehensive instructions for setting up the SaaS Dashboard application environment, including development, testing, and production configurations.

## Table of Contents

1. [System Requirements](#system-requirements)
2. [Environment Configuration](#environment-configuration)
3. [Database Setup](#database-setup)
4. [Application Setup](#application-setup)
5. [Module Configuration](#module-configuration)
6. [Testing Environment](#testing-environment)
7. [Production Deployment](#production-deployment)
8. [Troubleshooting](#troubleshooting)

## System Requirements

### Server Requirements

- **PHP**: 8.1 or higher
- **Composer**: 2.0 or higher
- **Node.js**: 16.0 or higher (for asset compilation)
- **NPM/Yarn**: Latest version
- **MySQL**: 8.0 or higher
- **Redis**: 6.0 or higher (for caching and queues)
- **Web Server**: Apache 2.4+ or Nginx 1.18+

### PHP Extensions

```bash
# Required PHP extensions
php-mysql
php-redis
php-gd
php-mbstring
php-xml
php-curl
php-zip
php-bcmath
php-intl
php-fileinfo
php-openssl
php-tokenizer
php-json
php-pdo
php-pdo_mysql
php-dom
php-filter
php-hash
php-iconv
php-mcrypt
php-simplexml
php-soap
php-xmlreader
php-xmlwriter
```

### Development Tools

- **Git**: Version control
- **Docker**: Containerization (optional)
- **VS Code**: Recommended IDE
- **Laravel Telescope**: Debugging (development only)

## Environment Configuration

### Environment Files

The application uses multiple environment files for different configurations:

```
.env                    # Main environment file
.env.example           # Template file
.env.testing           # Testing environment
.env.local             # Local development overrides
.env.production        # Production environment
```

### Key Environment Variables

#### Application Configuration

```env
# Application
APP_NAME="SaaS Dashboard"
APP_ENV=local
APP_KEY=base64:your-app-key-here
APP_DEBUG=true
APP_URL=http://localhost:8000

# Timezone
APP_TIMEZONE=UTC
```

#### Database Configuration

```env
# Landlord Database (Main Application)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=saas_landlord
DB_USERNAME=root
DB_PASSWORD=

# Tenant Database (Multi-tenant)
TENANT_DB_CONNECTION=mysql
TENANT_DB_HOST=127.0.0.1
TENANT_DB_PORT=3306
TENANT_DB_DATABASE=saas_tenant
TENANT_DB_USERNAME=root
TENANT_DB_PASSWORD=
```

#### Cache and Session Configuration

```env
# Cache
CACHE_DRIVER=redis
CACHE_PREFIX=saas_dashboard

# Session
SESSION_DRIVER=redis
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

# Redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
REDIS_DB=0
```

#### Queue Configuration

```env
# Queue
QUEUE_CONNECTION=redis
QUEUE_FAILED_DRIVER=database-uuids

# Horizon
HORIZON_PREFIX=saas_dashboard
```

#### Mail Configuration

```env
# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@saas-dashboard.com
MAIL_FROM_NAME="${APP_NAME}"
```

#### File Storage Configuration

```env
# Filesystem
FILESYSTEM_DISK=local
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false
```

#### Multi-tenant Configuration

```env
# Multi-tenancy
TENANT_DOMAIN=localhost
TENANT_SUBDOMAIN_SEPARATOR=.
TENANT_DATABASE_PREFIX=tenant_
TENANT_CONNECTION_NAME=tenant
```

## Database Setup

### Landlord Database

The landlord database contains the main application data:

```sql
-- Create landlord database
CREATE DATABASE saas_landlord CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create landlord user
CREATE USER 'saas_landlord'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON saas_landlord.* TO 'saas_landlord'@'localhost';
FLUSH PRIVILEGES;
```

### Tenant Database

The tenant database contains tenant-specific data:

```sql
-- Create tenant database
CREATE DATABASE saas_tenant CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create tenant user
CREATE USER 'saas_tenant'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON saas_tenant.* TO 'saas_tenant'@'localhost';
FLUSH PRIVILEGES;
```

### Database Migrations

```bash
# Run landlord migrations
php artisan migrate --database=landlord

# Run tenant migrations
php artisan migrate --database=tenant

# Run all migrations
php artisan migrate
```

## Application Setup

### Initial Installation

```bash
# Clone repository
git clone https://github.com/your-org/saas-dashboard.git
cd saas-dashboard

# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Create storage symlink
php artisan storage:link

# Compile assets
npm run dev
```

### Database Seeding

```bash
# Seed real data (languages, email templates, etc.)
php artisan seed:real-data

# Seed dummy data for development
php artisan seed:dummy-data

# Seed specific modules
php artisan seed:real-data --modules=Localization,Email
php artisan seed:dummy-data --modules=Auth,Utilities
```

### Module Installation

```bash
# Install all modules
php artisan module:install

# Install specific module
php artisan module:install Auth

# Enable module
php artisan module:enable Auth

# Disable module
php artisan module:disable Auth
```

## Module Configuration

### Module Structure

Each module follows a consistent structure:

```
modules/ModuleName/
├── Config/
├── Database/
│   ├── Migrations/
│   └── Seeders/
├── Entities/
├── Http/
│   ├── Controllers/
│   ├── Middleware/
│   └── Requests/
├── Providers/
├── Repositories/
├── Routes/
├── Services/
└── module.json
```

### Module Dependencies

Modules can have dependencies on other modules:

```json
{
    "name": "ModuleName",
    "alias": "modulename",
    "version": "1.0.0",
    "description": "Module description",
    "keywords": ["laravel", "module"],
    "order": 100,
    "providers": [
        "Modules\\ModuleName\\Providers\\ModuleNameServiceProvider"
    ],
    "aliases": {},
    "files": [],
    "requires": [
        "Auth",
        "Utilities"
    ]
}
```

## Testing Environment

### Test Database Setup

```env
# Testing environment
APP_ENV=testing
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=saas_dashboard_test
DB_USERNAME=root
DB_PASSWORD=
```

### Running Tests

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Unit
php artisan test --testsuite=Feature

# Run tests with coverage
php artisan test --coverage

# Run tests for specific module
php artisan test --filter=Email
```

### Test Configuration

```xml
<!-- phpunit.xml -->
<phpunit>
    <testsuites>
        <testsuite name="Unit">
            <directory>tests/Unit</directory>
        </testsuite>
        <testsuite name="Feature">
            <directory>tests/Feature</directory>
        </testsuite>
    </testsuites>
    <coverage>
        <include>
            <directory suffix=".php">app</directory>
            <directory suffix=".php">modules</directory>
        </include>
    </coverage>
</phpunit>
```

## Production Deployment

### Server Configuration

#### Nginx Configuration

```nginx
server {
    listen 80;
    server_name your-domain.com *.your-domain.com;
    root /var/www/saas-dashboard/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

#### Apache Configuration

```apache
<VirtualHost *:80>
    ServerName your-domain.com
    ServerAlias *.your-domain.com
    DocumentRoot /var/www/saas-dashboard/public

    <Directory /var/www/saas-dashboard/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/saas-dashboard_error.log
    CustomLog ${APACHE_LOG_DIR}/saas-dashboard_access.log combined
</VirtualHost>
```

### Production Environment

```env
# Production environment
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database
DB_CONNECTION=mysql
DB_HOST=your-db-host
DB_PORT=3306
DB_DATABASE=saas_landlord_prod
DB_USERNAME=saas_landlord
DB_PASSWORD=secure_production_password

# Cache
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Mail
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
```

### Deployment Script

```bash
#!/bin/bash
# deploy.sh

# Pull latest code
git pull origin main

# Install dependencies
composer install --no-dev --optimize-autoloader
npm ci --production

# Run migrations
php artisan migrate --force

# Clear caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Compile assets
npm run production

# Restart services
sudo systemctl restart nginx
sudo systemctl restart php8.1-fpm
sudo systemctl restart redis
```

## Troubleshooting

### Common Issues

#### 1. Permission Issues

```bash
# Fix storage permissions
sudo chown -R www-data:www-data storage
sudo chmod -R 775 storage

# Fix bootstrap cache permissions
sudo chown -R www-data:www-data bootstrap/cache
sudo chmod -R 775 bootstrap/cache
```

#### 2. Database Connection Issues

```bash
# Test database connection
php artisan tinker
>>> DB::connection()->getPdo();

# Check database configuration
php artisan config:show database
```

#### 3. Module Issues

```bash
# Clear module cache
php artisan module:cache-clear

# Reinstall modules
php artisan module:install --force
```

#### 4. Queue Issues

```bash
# Restart queue workers
php artisan queue:restart

# Check queue status
php artisan queue:work --verbose
```

#### 5. Cache Issues

```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Rebuild caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Debugging

#### Enable Debug Mode

```env
# Development debugging
APP_DEBUG=true
LOG_LEVEL=debug

# Telescope (development only)
TELESCOPE_ENABLED=true
```

#### Log Files

```bash
# View application logs
tail -f storage/logs/laravel.log

# View error logs
tail -f /var/log/nginx/error.log
tail -f /var/log/apache2/error.log
```

#### Performance Monitoring

```bash
# Install Telescope for debugging
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate

# Access Telescope at /telescope
```

### Security Considerations

#### Environment Security

```env
# Secure environment variables
APP_KEY=base64:your-secure-key-here
DB_PASSWORD=secure-database-password
MAIL_PASSWORD=secure-email-password
REDIS_PASSWORD=secure-redis-password
```

#### File Permissions

```bash
# Secure file permissions
chmod 644 .env
chmod 755 storage
chmod 755 bootstrap/cache
```

#### SSL Configuration

```nginx
# SSL configuration
server {
    listen 443 ssl http2;
    server_name your-domain.com *.your-domain.com;
    
    ssl_certificate /path/to/certificate.crt;
    ssl_certificate_key /path/to/private.key;
    
    # SSL security settings
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-RSA-AES256-GCM-SHA512:DHE-RSA-AES256-GCM-SHA512;
    ssl_prefer_server_ciphers off;
}
```

## Conclusion

This environment setup documentation provides comprehensive instructions for configuring the SaaS Dashboard application across different environments. Follow these guidelines to ensure proper setup and optimal performance.

### Key Points

- **System Requirements**: Ensure all required software and extensions are installed
- **Environment Configuration**: Properly configure environment variables for each environment
- **Database Setup**: Set up both landlord and tenant databases correctly
- **Module Configuration**: Understand module structure and dependencies
- **Testing**: Configure and run tests properly
- **Production**: Follow security best practices for production deployment
- **Troubleshooting**: Use common solutions for typical issues

### Maintenance

- Regularly update dependencies and security patches
- Monitor application performance and logs
- Backup databases and files regularly
- Keep environment documentation updated
- Follow security best practices
