# SaaS Dashboard - Multi-Tenant Administration Platform

A comprehensive multi-tenant administration dashboard built with Laravel 11, providing complete tenant management, subscription handling, payment processing, and customer relationship management capabilities.

### Quick Start

```bash
# One command: backend setup + frontend build + start server
./autorun.sh

# Options:
./autorun.sh --force            # Force rebuild everything
./autorun.sh --skip-backend     # Skip migrations/seeding
./autorun.sh --skip-frontend    # Skip frontend build
./autorun.sh --skip-build       # Skip npm build, just create symlinks
```

### Manual Frontend Build

```bash
# Build landlord frontend
cd landlord-frontend && npm install && npm run build

# Build tenant frontend
cd tenant-frontend && npm install && npm run build

# Create symlinks
cd backend/public
ln -sfn ../../landlord-frontend/out landlord-assets
ln -sfn ../../tenant-frontend/out tenant-assets

# Start Laravel
cd backend && php artisan serve --host=0.0.0.0
```

### Access URLs

| Role | URL | API Prefix |
|------|-----|------------|
| Landlord Admin | `http://landlord.saas.test:8000` | `/api/admin` |
| Tenant #1 | `http://customer1.saas.test:8000` | `/api` |
| Tenant #2 | `http://customer2.saas.test:8000` | `/api` |


## 🎯 Business Overview

### Purpose & Value Proposition

The SaaS Dashboard is the core administration system for managing a multi-tenant SaaS platform. It enables businesses to:

- **Manage Multiple Tenants**: Create, configure, and manage multiple customer organizations from a single interface
- **Handle Subscriptions**: Automate subscription lifecycle management including activations, renewals, upgrades, and cancellations
- **Process Payments**: Integrated payment processing with support for multiple payment gateways
- **Manage Customers**: Comprehensive customer relationship management with detailed profiles and interaction history
- **Control Access**: Role-based access control with granular permissions for different user types
- **Localize Content**: Multi-language support with translation management
- **Monitor Operations**: Real-time monitoring with Laravel Horizon, Telescope, and WebSocket support

### Target Users

- **Landlord Administrators**: Platform owners managing the entire multi-tenant system
- **Tenant Administrators**: Organization admins managing their tenant's configuration
- **Support Staff**: Customer support teams handling tenant issues and requests
- **Sales Teams**: Teams managing tenant onboarding and subscription sales

### Key Business Features

- **Multi-Tenancy Architecture**: Complete tenant isolation with shared infrastructure
- **Subscription Management**: Automated billing, invoicing, and subscription lifecycle
- **Payment Integration**: Multiple payment gateway support (Stripe, PayPal, etc.)
- **Customer Management**: Full CRM capabilities with customer profiles and history
- **Role & Permission System**: Granular access control with Spatie Permissions
- **Email Management**: Automated email processing and notifications
- **File Management**: Secure file storage with encryption support
- **Real-Time Updates**: WebSocket support for live dashboard updates
- **Audit Logging**: Complete audit trail of all system changes
- **Multi-Language Support**: Translation management with Google Translate integration

## 🏗️ Technical Architecture

### Tech Stack

- **Backend Framework**: Laravel 11 (PHP 8.2+)
- **Frontend**: Next.js 16 (static export) + shadcn/ui + Tailwind CSS 4
- **Database**: MySQL (Multi-database architecture: Landlord + Tenant databases)
- **Queue System**: Laravel Horizon with Redis
- **Real-Time**: WebSocket server (Node.js)
- **Authentication**: Laravel Passport (API), Session (Web)
- **File Storage**: AWS S3, Local storage with encryption support
- **Monitoring**: Laravel Telescope, Laravel Horizon
- **Module System**: nwidart/laravel-modules

### Architecture Pattern

The dashboard follows a **Landlord-Tenant** multi-tenancy pattern:

- **Landlord Database**: Stores platform-wide data (tenants, modules, configurations)
- **Tenant Databases**: Isolated databases for each tenant organization
- **Shared Modules**: Reusable modules that work across all tenants
- **Tenant Context**: Automatic database switching based on tenant domain

### Module System

The platform uses a modular architecture with the following core modules:

- **Auth**: Authentication, authorization, 2FA, user management
- **Tenant**: Tenant management and provisioning
- **Customer**: Customer relationship management
- **Subscription**: Subscription lifecycle management
- **Payment**: Payment processing and invoicing
- **Email**: Email processing and notifications
- **Geography**: Geographic data management (Countries, Provinces, Cities, Towns, Streets)
- **Localization**: Translation and language management
- **Utilities**: Tags, Types, Categories, Industries
- **FileManager**: File upload, storage, and management
- **Notification**: In-app and push notifications
- **Development**: Development tools and configurations

## 📋 Table of Contents

- [Installation](#installation)
- [Quick Start](#quick-start)
- [Database Setup](#database-setup)
- [Module System](#module-system)
- [Commands](#commands)
- [Development Workflow](#development-workflow)
- [Production Deployment](#production-deployment)
- [Architecture Details](#architecture-details)
- [Global Utilities](#global-utilities)

## 🚀 Installation

### Prerequisites

- **PHP 8.2+** with extensions: `php-fpm`, `php-mysql`, `php-xml`, `php-mbstring`, `php-curl`, `php-zip`, `php-gd`
- **Composer** (PHP package manager)
- **Node.js 18+** and npm
- **MySQL** database server
- **Redis** (for queues and caching)

### Step 1: Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### Step 2: Environment Configuration

```bash
cp .env.example .env
php artisan key:generate
```

Configure your `.env` file with:
- Database connections (landlord and tenant)
- Redis configuration
- AWS S3 credentials (if using cloud storage)
- Mail configuration
- Payment gateway credentials

### Step 3: Database Setup

```bash
# Create storage symlink
php artisan storage:link

# Run landlord migrations
php artisan landlord:migrate

# Seed initial data
php artisan db:seed --class=Database\\Seeders\\Landlord\\RolePermissionSeeder
php artisan db:seed --class=Database\\Seeders\\Landlord\\LandlordTenantSeeder
php artisan db:seed --class=Modules\\Auth\\Database\\Seeders\\LandlordUserSeeder
php artisan db:seed --class=Modules\\Utilities\\Database\\Seeders\\ModulesSeeder
php artisan db:seed --class=Modules\\Development\\Database\\Seeders\\ConfigurationsSeeder
```

## 🏃 Quick Start

### Development Mode

Start all development services with a single command:

```bash
composer run dev
```

This starts:
- Laravel development server
- Queue worker (Laravel Horizon)
- Log monitoring (Laravel Pail)
- Vite development server (React)

### Manual Start

If you prefer to run services separately:

```bash
# Terminal 1: Laravel server
php artisan serve

# Terminal 2: Queue worker
php artisan queue:listen

# Terminal 3: WebSocket server
cd websocket && node server.js

# Terminal 4: Vite dev server
npm run dev
```

## 💾 Database Setup

### Multi-Database Architecture

The system uses two types of databases:

1. **Landlord Database**: Platform-wide data
   - Tenants table
   - Modules configuration
   - Global settings
   - Landlord users

2. **Tenant Databases**: Isolated per-tenant data
   - Tenant-specific users
   - Tenant data and records
   - Tenant configurations

### Running Migrations

#### Landlord Migrations

```bash
# Option 1: Use custom command
php artisan landlord:migrate

# Option 2: Manual migration
php artisan migrate --path=database/migrations/landlord --database=landlord
php artisan migrate --path=modules/*/Database/Migrations/landlord --database=landlord
php artisan migrate --path=modules/*/Database/migrations/landlord --database=landlord
php artisan migrate --path=modules/*/Database/migrations/shared --database=landlord
php artisan migrate --path=modules/*/Database/Migrations/shared --database=landlord
php artisan migrate --path=database/migrations/shared --database=landlord
```

#### Tenant Migrations

Tenant migrations run automatically when a tenant is created, or manually:

```bash
php artisan tenants:migrate
```

### Database Seeding

#### Landlord Seeders

```bash
# Roles and permissions
php artisan db:seed --class=Database\\Seeders\\Landlord\\RolePermissionSeeder

# Create landlord tenant record
php artisan db:seed --class=Database\\Seeders\\Landlord\\LandlordTenantSeeder

# Create default landlord user
php artisan db:seed --class=Modules\\Auth\\Database\\Seeders\\LandlordUserSeeder

# Seed modules
php artisan db:seed --class=Modules\\Utilities\\Database\\Seeders\\ModulesSeeder

# Seed configurations
php artisan db:seed --class=Modules\\Development\\Database\\Seeders\\ConfigurationsSeeder
```

## 📦 Module System

### Available Modules

The platform includes the following modules (see `modules_statuses.json`):

- **Tenant**: Tenant management
- **Auth**: Authentication and authorization
- **Customer**: Customer management
- **Geography**: Geographic data
- **Localization**: Translation management
- **Configuration**: System configuration
- **Email**: Email processing
- **Utilities**: Tags, types, categories
- **Subscription**: Subscription management
- **Payment**: Payment processing
- **Development**: Development tools
- **FileManager**: File management
- **Notifications**: Notification system

### Module Structure

Each module follows a standard structure:

```
ModuleName/
├── Config/
├── Database/
│   ├── Migrations/
│   │   ├── landlord/
│   │   ├── tenant/
│   │   └── shared/
│   └── Seeders/
├── Entities/
├── Http/
│   ├── Controllers/
│   ├── Middleware/
│   └── Requests/
├── Models/
├── Providers/
├── Resources/
│   ├── assets/
│   └── views/
└── Routes/
```

## 🔧 Commands

### Application Commands

#### Start Application

Runs all necessary services to start the application:

```bash
php artisan app:start
```

#### Backup Application

Backs up the application and saves it to cloud storage:

```bash
php artisan app:backup
```

#### Sync Missing Translations

Automatically translates missing translation keys using Google Translate API:

```bash
php artisan translations:translate-missing
```

### Tenant Management

```bash
# List all tenants
php artisan tenants:list

# Create a new tenant
php artisan tenants:create

# Run migrations for all tenants
php artisan tenants:migrate

# Run seeders for all tenants
php artisan tenants:seed
```

### Email Processing

Process emails for tenants:

```bash
# Process for specific tenant
php artisan process:emails --tenant=customer1

# Process for all tenants
php artisan process:emails
```

## 🔄 Development Workflow

### Code Structure

- **App Core**: `app/` - Core application logic
- **Modules**: `modules/` - Feature modules
- **Routes**: `routes/` - Route definitions
- **Resources**: `resources/` - Views, assets, React components
- **Config**: `config/` - Configuration files

### Next.js Frontend

The dashboard includes two separate Next.js frontends (landlord + tenant):

```bash
# Build both frontends
./autorun.sh

# Or build individually
cd landlord-frontend && npm install && npm run build
cd tenant-frontend && npm install && npm run build
```

### Development Best Practices

1. **Module Development**: Create new features as modules
2. **Database Migrations**: Use separate migrations for landlord/tenant/shared
3. **Translation**: Use translation helpers for all user-facing text
4. **Permissions**: Define permissions in plural form
5. **Roles**: Define roles in singular form
6. **Routes**: Use plural form for resource routes

## 🚀 Production Deployment

### Pre-Deployment Checklist

1. **Environment Configuration**:
   ```bash
   APP_ENV=production
   APP_DEBUG=false
   ```

2. **Optimize Application**:
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   php artisan event:cache
   ```

3. **Build Assets**:
   ```bash
   npm run build
   ```

4. **Run Migrations**:
   ```bash
   php artisan landlord:migrate
   php artisan tenants:migrate
   ```

### Cron Jobs

Set up the Laravel scheduler:

```bash
* * * * * cd /var/www/PROJECT_NAME && php artisan schedule:run >> /dev/null 2>&1
```

### Queue Workers

Run queue workers in production:

```bash
php artisan horizon
```

Or use supervisor for process management.

### WebSocket Server

Run the WebSocket server:

```bash
cd websocket
node server.js
```

Or use PM2 for process management:

```bash
pm2 start websocket/server.js --name websocket
```

## 🔌 Web Sockets

### Running the Queue Listener

Laravel uses a queue system to broadcast events:

```bash
php artisan queue:listen
```

### Starting the WebSocket Server

Navigate to the WebSocket directory and start the server:

```bash
cd websocket
node server.js
```

The WebSocket server handles real-time updates for the dashboard.

## 🎨 Global Utilities

### Global CSS Classes

- `.slug-input` - Automatic slug generation
- `.snake-input` - Snake case conversion
- `.decimal-input` - Decimal number formatting
- `.emoji-input` - Emoji picker integration
- `.open-create-modal` - Open create modal
- `.open-edit-modal` - Open edit modal
- `.open-details-btn` - Open details view
- `.select-row` - Datatable row selection
- `.upload-image` - Image upload handler
- `.view-image` - Image viewer
- `.file-uploader` - File upload component
- `.generate-password-input` - Password generator

### Global IDs

- `#table` - Datatables container
- `#ckInput` - CKEditor input fields

### Global Data Attributes

- `data-toggle="tooltip"` - Bootstrap tooltip
- `data-selectable="true"` - Enable row selection
- `data-button-listen="select-row"` - Button row selection listener

### Global Functions

#### PHP Functions

- `translate('key', ['attribute' => 'value'], 'en')` - Translate dashboard content
- `@translate()` - Blade directive for translation
- `__()` - Short translation function
- `configuration()` - Get configuration value from database
- `@configuration` - Blade directive for configuration
- `render_number()` - Format numbers (e.g., 1,000,000 → "1m")

#### JavaScript Functions

- `translate('home')` - Translate key
- `t('home')` - Short translation function
- `t('unknown_key', { var1: 'test1' })` - Translate with variables

### File Handler

Use the `FileHandler` trait in models:

```php
use FileHandler;

protected $fileColumns = [
    'icon' => [
        'is_encrypted' => false,
        'access_level' => 'public',
        'metadata' => ['width', 'height', 'aspect_ratio'],
    ],
    'thumbnail', // Simple configuration
];

public function getIconAttribute($value)
{
    return $this->getFileUrl($value);
}

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

### Translatable Models

Use the `Translatable` trait for multi-language support:

```php
use Translatable;

protected $translatableColumns = ['name', 'description'];
```

In datatables:

```php
->editColumn('description', function($row) {
    return TranslateHelper::returnTranslatableEditor($row, 'description');
})
```

## 📊 Data Models

### Geography Hierarchy

- **Countries**
  - Provinces
    - Cities
      - Towns
        - Streets

### Utilities

#### Tag System
- Size: Small, Medium, Large
- Color: Red, Blue, Green
- Material: Cotton, Polyester, Metal

#### Type System
- Music: Jazz, Rock, Classical
- Strategy: Real-time, Turn-based
- Format: Digital, Physical

#### Categories
- Books
- Movies
- Games

#### Industries
- Technology
- Healthcare
- Education

## 📝 Naming Conventions

- **Permission Names**: Plural (e.g., `users.create`, `users.edit`)
- **Role Names**: Singular (e.g., `admin`, `manager`)
- **Route Names**: Plural (e.g., `/users`, `/customers`)

## 🔧 Troubleshooting

### Common Issues

1. **Storage Link**:
   ```bash
   php artisan storage:link
   ```

2. **Permission Issues**:
   ```bash
   chmod -R 775 storage bootstrap/cache
   ```

3. **Git File Mode**:
   ```bash
   git config core.fileMode false
   ```

4. **Cache Issues**:
   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan route:clear
   php artisan view:clear
   ```

## 📚 Additional Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Laravel Modules Documentation](https://nwidart.com/laravel-modules)
- [Spatie Multitenancy](https://spatie.be/docs/laravel-multitenancy)
- [Laravel Horizon](https://laravel.com/docs/horizon)

## 🔗 Related Projects

- **[SaaS Website](../saas-website/)** - Public website and customer portal
- **[SaaS Desktop App](../saas-desktop-app/)** - Desktop application
- **[SaaS Mobile App](../saas-mobile-app/)** - Mobile application

---

**Built with ❤️ using Laravel 11 and Next.js**
