# SaaS - All in one

# Table of Contents

-   [Installation](#installation)
-   [Modules Requirements](#modules-requirements)
-   [Commands](#commands)
    -   [Start App Command](#start-app-command)
    -   [Backup App Command](#backup-app-command)
    -   [Sync Missing Language Translations](#sync-missing-language-translations)
    -   [Generate New Tenant Command](#generate-new-tenant-command)
    -   [Create Super Admin User Command](#create-super-admin-user-command)
-   [Databases](#databases)
-   [Troubleshooting](#troubleshooting)
-   [Running Landlord-Specific Migrations](#running-landlord-specific-migrations)
    -   [Option 1: Direct Migration Commands](#option-1-direct-migration-commands)
    -   [Option 2: Custom Artisan Command](#option-2-custom-artisan-command)
    -   [Seed Role and Permissions (Landlord)](#seed-role-and-permissions-landlord)
    -   [Landlord Tenant Seeder (Landlord)](#landlord-tenant-seeder-landlord)
    -   [Seed Default Landlord User (Landlord)](#seed-default-landlord-user-landlord)
    -   [Seed Modules (Landlord)](#seed-modules-landlord)
    -   [Seed Configurations (Landlord)](#seed-configurations-landlord)
-   [Cron Jobs](#cron-jobs)
-   [Web Sockets](#web-sockets)
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

1. **Create storage symlink**:
   ```bash
   php artisan storage:link
   ```

2. **Generate OAuth2 keys** (Required for API authentication):
   ```bash
   # Fix storage permissions first
   sudo chown -R www-data:www-data storage/
   sudo chmod -R 775 storage/
   
   # Generate OAuth2 keys
   sudo -u www-data php artisan passport:keys --force
   
   # Create personal access client
   sudo -u www-data php artisan passport:client --personal --name="SaaS Dashboard Personal Access Client"
   ```

   **Note**: If you encounter "Personal access client not found" error after login, this step is required to generate the OAuth2 public and private keys for API authentication. The tenant setup commands now automatically handle Passport setup for new tenants.

3. **Configure Environment Variables**:
   ```bash
   # Copy environment file
   cp .env.example .env
   
   # Set proper APP_URL (important for asset loading)
   # Replace with your actual domain
   sed -i 's|APP_URL=http://localhost|APP_URL=http://landlord.saas.test|' .env
   
   # Set landlord organization name (for login validation)
   echo 'APP_LANDLORD_ORGANIZATION_NAME="Test SaaS Organization"' >> .env
   
   # Set default landlord user credentials
   echo 'DEFAULT_LANDLORD_NAME="Test Landlord"' >> .env
   echo 'DEFAULT_LANDLORD_EMAIL="landlord@test.com"' >> .env
   echo 'DEFAULT_LANDLORD_USERNAME="test_landlord"' >> .env
   echo 'DEFAULT_LANDLORD_PASSWORD="password123"' >> .env
   ```

4. **Fix File Permissions** (Critical for development):
   ```bash
   # Set proper ownership for development
   sudo chown -R abdelrahman:abdelrahman /var/www/saas-dashboard
   
   # Set directory permissions
   sudo chmod -R 755 /var/www/saas-dashboard
   
   # Set writable permissions for Laravel directories
   sudo chmod -R 775 storage/ bootstrap/cache/
   
   # Add user to www-data group for web server access
   sudo usermod -a -G www-data abdelrahman
   
   # Set group ownership for web server directories
   sudo chgrp -R www-data storage/ bootstrap/cache/
   sudo chmod -R g+w storage/ bootstrap/cache/
   ```

5. **Configure Session Driver** (Prevents database connection issues):
   ```bash
   # Use file-based sessions to avoid multi-tenancy conflicts
   echo 'SESSION_DRIVER=file' >> .env
   ```

6. **Clear Configuration Cache**:
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

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

### Email Template Management

Manage dynamic email templates through the command line interface

```bash
# List all email templates
php artisan email:templates list

# Create a new email template
php artisan email:templates create --name="Welcome Email" --subject="Welcome!" --description="Welcome new users" --body="<h1>Welcome {{name}}!</h1>"

# Update an existing template
php artisan email:templates update --id=1 --subject="Updated Welcome Subject"

# Delete a template
php artisan email:templates delete --id=1

# Seed default email templates
php artisan email:templates seed
```

For detailed documentation on the email system, see [Email System Documentation](documentation/email-system.md).

### Landlord Database Setup

Complete landlord database setup with migrations and seeding in one command

```bash
# Complete setup (migrations + seeding)
php artisan landlord:setup

# Force setup without confirmation
php artisan landlord:setup --force

# Run migrations only
php artisan landlord:setup --migrate-only

# Run seeding only
php artisan landlord:setup --seed-only

# Skip real data seeding
php artisan landlord:setup --skip-real-data

# Skip dummy data seeding
php artisan landlord:setup --skip-dummy-data
```

This command will:
- Run all landlord migrations from various paths
- Seed essential real data (roles, permissions, users, languages, email templates, etc.)
- Seed dummy data for development and testing
- Display next steps and helpful information

For detailed documentation on the landlord setup, see [Environment Setup Documentation](documentation/environment-setup.md).

---

## Databases

---

## Running Landlord-Specific Migrations

### Option 1: Complete Setup Command (Recommended)

Use the comprehensive landlord setup command that handles both migrations and seeding:

```bash
# Complete setup (migrations + seeding)
php artisan landlord:setup

# Force setup without confirmation
php artisan landlord:setup --force
```

### Option 2: Direct Migration Commands

Run individual migration commands for the landlord database:

```bash
php artisan migrate --path=database/migrations/landlord --database=landlord
php artisan migrate --path=modules/*/Database/Migrations/landlord --database=landlord
php artisan migrate --path=modules/*/Database/migrations/landlord --database=landlord
php artisan migrate --path=modules/*/Database/migrations/shared --database=landlord
php artisan migrate --path=modules/*/Database/Migrations/shared --database=landlord
php artisan migrate --path=database/migrations/shared --database=landlord
```

### Option 3: Custom Artisan Command

You can simplify the migration process by using a custom Artisan command:

```bash
php artisan landlord:migrate
```

### Individual Seeding Commands

If you need to run individual seeders (the `landlord:setup` command handles all of these automatically):

#### Seed Role and Permissions (Landlord)

```bash
php artisan db:seed --class=Database\\Seeders\\Landlord\\RolePermissionSeeder
```

#### Seed Languages

```bash
# Run the language seeder
php artisan db:seed --class="Modules\Localization\Database\Seeders\LanguageSeeder" --database=landlord

# Or use the convenience script
./seed-languages.sh
```

This will seed the following languages:
- **English** (en) - Left-to-right
- **Arabic** (ar) - Right-to-left  
- **German** (de) - Left-to-right

#### Landlord Tenant Seeder (Landlord)

Creates a landlord row in the tenants table:

```bash
php artisan db:seed --class=Database\\Seeders\\Landlord\\LandlordTenantSeeder
```

#### Seed Default Landlord User (Landlord)

Creates a user row in the landlord tenant table:

```bash
php artisan db:seed --class=Modules\\Auth\\Database\\Seeders\\LandlordUserSeeder
```

**Default Login Credentials** (after running the seeder):
- **Organization**: `Test SaaS Organization`
- **Username**: `test_landlord`
- **Password**: `password123`
- **Email**: `landlord@test.com`

**Note**: These credentials are set in the `.env` file and can be customized before running the seeder.

#### Seed Modules (Landlord)

```bash
php artisan db:seed --class=Modules\\Utilities\\Database\\Seeders\\ModulesSeeder
```

#### Seed Configurations (Landlord)

```bash
php artisan db:seed --class=Modules\\Development\\Database\\Seeders\\ConfigurationsSeeder
```

### Quick Setup Commands

For development and testing, you can also use these convenience commands:

```bash
# Seed real data (languages, email templates, configurations, etc.)
php artisan seed:real-data

# Seed dummy data for development and testing
php artisan seed:dummy-data

# Seed specific modules only
php artisan seed:real-data --modules=Localization,Email
php artisan seed:dummy-data --modules=Auth,Utilities
```

---

## Tenant Database Setup

### Complete Tenant Setup Command (Recommended)

Use the comprehensive tenant setup command that handles both migrations and seeding for tenant databases (excluding landlord):

```bash
# Setup all tenants (migrations + seeding)
php artisan tenant:setup

# Setup specific tenant by ID
php artisan tenant:setup 1

# Setup specific tenant by name
php artisan tenant:setup customer1

# Force setup without confirmation
php artisan tenant:setup --force

# Run migrations only (skip seeding)
php artisan tenant:setup --migrate-only

# Run seeding only (skip migrations)
php artisan tenant:setup --seed-only

# Run fresh migrations (drop all tables and re-run)
php artisan tenant:setup --fresh
```

This command will:
- Run all tenant migrations from various paths
- Seed tenant database with initial data
- Setup Laravel Passport (OAuth2 keys and personal access client)
- Handle multiple tenants or specific tenant (excluding landlord)
- Display progress and helpful information

### Generate New Tenant Command

Create a complete tenant setup with specified modules, fake brand data, and nginx configuration:

```bash
# Basic tenant generation with HR and CRM modules
php artisan tenant:generate --name=mycompany --modules=hr,crm

# Advanced tenant with multiple modules
php artisan tenant:generate \
  --name=techcorp \
  --modules=hr,crm,ticket,accounting,inventory,sales \
  --domain=techcorp.local \
  --database=saas_techcorp \
  --force

# Interactive mode (will prompt for inputs)
php artisan tenant:generate
```

#### Parameters

| Parameter | Description | Required | Example |
|-----------|-------------|----------|---------|
| `--name` | Tenant name | Yes | `--name=mycompany` |
| `--modules` | Comma-separated modules | Yes | `--modules=hr,crm,ticket` |
| `--domain` | Custom domain | No | `--domain=mycompany.local` |
| `--database` | Custom database name | No | `--database=saas_mycompany` |
| `--force` | Overwrite existing tenant | No | `--force` |

#### Available Modules

| Module | Description | Module | Description |
|--------|-------------|--------|-------------|
| `hr` | Human Resources | `email` | Email Management |
| `crm` | Customer Relationship Management | `notification` | Notification System |
| `ticket` | Support Ticket System | `filemanager` | File Management |
| `accounting` | Financial Accounting | `utilities` | System Utilities |
| `inventory` | Inventory Management | `geography` | Geographic Data |
| `sales` | Sales Management | `localization` | Multi-language Support |
| `reporting` | Reports & Analytics | `payment` | Payment Processing |
| `subscription` | Subscription Management | `development` | Development Tools |
| `customer` | Customer Management | `tenant` | Tenant Management |
| `auth` | Authentication | `api` | API Management |
| `comment` | Comment System | `workflow` | Workflow Management |
| `staticpages` | Static Pages | `monitoring` | System Monitoring |

#### What the Command Creates

1. **Tenant Record**: Complete tenant setup in landlord database
2. **Fake Brand**: Realistic company data with random brand name from 20 predefined names
3. **Customer Record**: Links brand to customer with proper relationships
4. **Database Setup**: Runs all migrations and seeders for the tenant
5. **Nginx Configuration**: Generates and enables nginx configuration
6. **Service Management**: Attempts to restart nginx service

#### Example Output

```
🚀 Starting Tenant Generation Process...

📋 Generation Plan:
   Tenant Name: mycompany
   Brand Name: TechCorp Solutions
   Domain: mycompany.saas.test
   Database: saas_mycompany
   Modules: HR, CRM

🏗️  Creating tenant...
   ✅ Tenant created: mycompany (ID: 12)

👤 Creating customer...
   ✅ Customer created: TechCorp Solutions (ID: 8)

🏢 Creating brand...
   ✅ Brand created: TechCorp Solutions (ID: 5)

🗄️  Setting up tenant database...
   Running migrations...
   ✅ Database setup completed

🌐 Generating nginx configuration...
   ✅ Nginx config written to: /etc/nginx/sites-available/mycompany.saas.test
   ✅ Symlink created: /etc/nginx/sites-enabled/mycompany.saas.test

🔄 Restarting nginx...
   ✅ Nginx restarted successfully

🎉 Tenant generation completed successfully!

📋 Summary:
   Tenant: mycompany
   Domain: http://mycompany.saas.test
   Database: saas_mycompany
   Brand: TechCorp Solutions
   Modules: HR, CRM

🔧 Next Steps:
   1. Update your /etc/hosts file: 127.0.0.1 mycompany.saas.test
   2. Access the tenant at: http://mycompany.saas.test
   3. Create a super admin user: php artisan tenant:create-super-admin mycompany
```

#### Post-Generation Steps

1. **Update Hosts File**:
   ```bash
   echo "127.0.0.1 mycompany.saas.test" | sudo tee -a /etc/hosts
   ```

2. **Create Super Admin User**:
   ```bash
   php artisan tenant:create-super-admin mycompany \
     --name="Super Admin" \
     --email="admin@mycompany.local" \
     --username="admin" \
     --password="password123"
   ```

3. **Access the Tenant**: Visit `http://mycompany.saas.test`

### Create Super Admin User Command

Create a super admin user for a specific tenant:

```bash
# Basic super admin creation
php artisan tenant:create-super-admin mycompany

# Advanced super admin with custom details
php artisan tenant:create-super-admin mycompany \
  --name="Super Admin" \
  --email="admin@mycompany.local" \
  --username="admin" \
  --password="password123"
```

### Manual Tenant Migration Commands

If you need to run individual migration commands for tenant databases:

```bash
# Migrate tenant-specific migrations
php artisan tenants:artisan "migrate --path=database/migrations/tenant --database=tenant" --tenant=1

# Migrate module tenant migrations
php artisan tenants:artisan "migrate --path=modules/*/Database/Migrations/tenant --database=tenant" --tenant=1
php artisan tenants:artisan "migrate --path=modules/*/Database/migrations/tenant --database=tenant" --tenant=1

# Migrate shared migrations to tenant
php artisan tenants:artisan "migrate --path=modules/*/Database/migrations/shared --database=tenant" --tenant=1
php artisan tenants:artisan "migrate --path=modules/*/Database/Migrations/shared --database=tenant" --tenant=1

# Seed tenant database
php artisan tenants:artisan "migrate --database=tenant --seed" --tenant=1
```

### Tenant Seeding

The tenant setup command automatically seeds the following data:

- **User Management**: Default tenant users and roles
- **System Configuration**: Tenant-specific settings
- **Module Data**: Initial data for all enabled modules
- **Sample Data**: Development and testing data (if available)

### Current Tenant Information

- **Tenant ID**: 1 (customer1)
- **Database**: saas_customer1
- **Domain**: customer1.saas.test

---

## Cron Jobs

Email Processing:

1.Running for a Specific Tenant
To process emails for a single tenant, use the --tenant option followed by the tenant’s domain.

```bash
php artisan process:emails --tenant=customer1
```

2. Running for All Tenants
   To process emails for all tenants in the system, run:

```bash
php artisan process:emails
```

---

## Web Sockets

### Running the Queue Listener

Laravel uses a queue system to broadcast events. To start the queue worker, run:

```bash
php artisan queue:listen
```

This will process any queued jobs related to WebSockets and broadcasting.

### Starting the WebSocket Server

Navigate to the WebSocket server directory:

```bash
cd websocket
```

Start the WebSocket server using Node.js:

```bash
node server.js
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

-   `$pagination->render('layouts.pagination.default')`

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

## Troubleshooting

### Common Installation Issues

#### 1. CSS/JS Assets Not Loading (404 errors)
**Problem**: CSS and JavaScript files return 404 Not Found errors.

**Solution**:
```bash
# Check and fix APP_URL in .env
grep APP_URL .env
# Should be: APP_URL=http://landlord.saas.test (or your actual domain)

# If incorrect, fix it:
sed -i 's|APP_URL=.*|APP_URL=http://landlord.saas.test|' .env

# Clear cache
php artisan config:clear
php artisan cache:clear
```

#### 2. "Invalid key supplied" Error After Login
**Problem**: OAuth2 authentication fails with "Invalid key supplied" error.

**Solution**:
```bash
# Generate OAuth2 keys (see Installation step 2)
sudo -u www-data php artisan passport:keys --force
sudo -u www-data php artisan passport:client --personal --name="SaaS Dashboard Personal Access Client"
```

#### 3. Database Connection Errors
**Problem**: `SQLSTATE[3D000]: Invalid catalog name: 1046 No database selected`

**Solution**:
```bash
# Use file-based sessions to avoid multi-tenancy conflicts
echo 'SESSION_DRIVER=file' >> .env

# Clear cache
php artisan config:clear
php artisan cache:clear
```

#### 4. Permission Denied Errors
**Problem**: Cannot save files, run commands, or access storage.

**Solution**:
```bash
# Fix ownership and permissions (see Installation step 4)
sudo chown -R abdelrahman:abdelrahman /var/www/saas-dashboard
sudo chmod -R 755 /var/www/saas-dashboard
sudo chmod -R 775 storage/ bootstrap/cache/
sudo chgrp -R www-data storage/ bootstrap/cache/
sudo chmod -R g+w storage/ bootstrap/cache/
```

#### 5. Git Tracking File Permission Changes
**Problem**: Git shows hundreds of "changed" files due to permission changes.

**Solution**:
```bash
# Disable file mode tracking
git config core.fileMode false

# If .git/config is owned by root, fix ownership first
sudo chown abdelrahman:abdelrahman .git/config
git config core.fileMode false
```

#### 6. Seeder Permission Issues
**Problem**: `php artisan db:seed` fails with permission errors.

**Solution**:
```bash
# Fix storage permissions
sudo chown -R www-data:www-data storage/
sudo chmod -R 775 storage/

# Run seeder with proper database connection
php artisan db:seed --class="Modules\Auth\Database\Seeders\LandlordUserSeeder" --database=landlord
```

### Tenant Generation Issues

#### 1. Permission Denied for Nginx Configuration
**Problem**: `Could not write nginx config: Permission denied`

**Solution**:
```bash
# Fix nginx directory permissions
sudo chown -R $USER:$USER /etc/nginx/sites-available/
sudo chown -R $USER:$USER /etc/nginx/sites-enabled/

# Or run the command with sudo
sudo php artisan tenant:generate --name=mycompany --modules=hr,crm
```

#### 2. Database Already Exists Error
**Problem**: `Database 'saas_mycompany' already exists`

**Solution**:
```bash
# Use --force flag to overwrite existing tenant
php artisan tenant:generate --name=mycompany --modules=hr,crm --force

# Or use a different name
php artisan tenant:generate --name=mycompany2 --modules=hr,crm
```

#### 3. Module Not Found Error
**Problem**: `Module 'invalidmodule' not found`

**Solution**:
```bash
# Check available modules and use correct module keys
# Available modules: hr, crm, ticket, accounting, inventory, sales, reporting, email, notification, filemanager, utilities, geography, localization, payment, subscription, development, customer, tenant, auth, api, comment, workflow, staticpages, monitoring

# Use correct module names
php artisan tenant:generate --name=mycompany --modules=hr,crm,ticket
```

#### 4. Nginx Configuration Test Failed
**Problem**: `nginx: configuration file test failed`

**Solution**:
```bash
# Test nginx configuration
sudo nginx -t

# Fix any syntax errors in the generated config
sudo nano /etc/nginx/sites-available/mycompany.saas.test

# Restart nginx
sudo systemctl restart nginx
```

#### 5. Domain Not Accessible After Generation
**Problem**: Cannot access the generated tenant domain

**Solution**:
```bash
# Add domain to hosts file
echo "127.0.0.1 mycompany.saas.test" | sudo tee -a /etc/hosts

# Check nginx configuration
sudo nginx -t
sudo systemctl status nginx

# Restart nginx if needed
sudo systemctl restart nginx
```

#### 6. Super Admin User Creation Failed
**Problem**: Cannot create super admin user for tenant

**Solution**:
```bash
# Create super admin user manually
php artisan tenant:create-super-admin mycompany \
  --name="Super Admin" \
  --email="admin@mycompany.local" \
  --username="admin" \
  --password="password123"

# Or use the seeder directly
php artisan tenants:artisan "db:seed --class=Database\\Seeders\\Tenant\\SuperAdminSeeder --database=tenant" --tenant=1
```

### Multi-Tenancy Configuration

The application uses a multi-tenant architecture with the following key components:

- **Landlord Database**: Main application database
- **Tenant Databases**: Individual tenant databases
- **Subdomain Routing**: `landlord.saas.test` for landlord, `tenant.saas.test` for tenants
- **Database Connection Middleware**: Automatically switches connections based on subdomain

**Important**: Always use the `landlord` database connection for seeders and migrations unless specifically working with tenant data.

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
