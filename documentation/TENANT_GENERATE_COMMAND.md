# Tenant Generation Command Documentation

## Command: `php artisan tenant:generate`

This command creates a complete tenant setup with specified modules, fake brand data, and nginx configuration.

## Usage

### Basic Syntax
```bash
php artisan tenant:generate --name=<tenant_name> --modules=<module_list>
```

### Parameters

| Parameter | Type | Required | Description | Example |
|-----------|------|----------|-------------|---------|
| `--name` | string | Yes | Name of the tenant to create | `--name=mycompany` |
| `--modules` | string | Yes | Comma-separated list of modules | `--modules=hr,crm,ticket` |
| `--domain` | string | No | Custom domain (optional) | `--domain=mycompany.local` |
| `--database` | string | No | Custom database name (optional) | `--database=saas_mycompany` |
| `--force` | flag | No | Overwrite existing tenant | `--force` |

## Examples

### Example 1: Basic Tenant Creation
```bash
php artisan tenant:generate --name=tenant1 --modules=hr,crm
```

**What this creates:**
- Tenant name: `tenant1`
- Domain: `tenant1.saas.test`
- Database: `saas_tenant1`
- Brand: Random fake brand name (e.g., "TechCorp Solutions")
- Modules: HR, CRM

### Example 2: Advanced Tenant Creation
```bash
php artisan tenant:generate \
  --name=mycompany \
  --modules=hr,crm,ticket,accounting,inventory \
  --domain=mycompany.local \
  --database=saas_mycompany \
  --force
```

**What this creates:**
- Tenant name: `mycompany`
- Domain: `mycompany.local`
- Database: `saas_mycompany`
- Brand: Random fake brand name
- Modules: HR, CRM, Ticket, Accounting, Inventory
- Overwrites existing tenant if it exists

### Example 3: Interactive Mode
```bash
php artisan tenant:generate
```

The command will prompt for:
- Tenant name
- Modules (comma-separated)

## Available Modules

| Module Key | Module Name | Description |
|------------|-------------|-------------|
| `hr` | HR | Human Resources Management |
| `crm` | CRM | Customer Relationship Management |
| `ticket` | Ticket | Support Ticket System |
| `accounting` | Accounting | Financial Accounting |
| `inventory` | Inventory | Inventory Management |
| `sales` | Sales | Sales Management |
| `reporting` | Reporting | Reports & Analytics |
| `email` | Email | Email Management |
| `notification` | Notification | Notification System |
| `filemanager` | FileManager | File Management |
| `utilities` | Utilities | System Utilities |
| `geography` | Geography | Geographic Data |
| `localization` | Localization | Multi-language Support |
| `payment` | Payment | Payment Processing |
| `subscription` | Subscription | Subscription Management |
| `development` | Development | Development Tools |
| `customer` | Customer | Customer Management |
| `tenant` | Tenant | Tenant Management |
| `auth` | Auth | Authentication |
| `api` | API | API Management |
| `comment` | Comment | Comment System |
| `workflow` | Workflow | Workflow Management |
| `staticpages` | StaticPages | Static Pages |
| `monitoring` | Monitoring | System Monitoring |

## What the Command Does

### Step 1: Tenant Creation
- Creates tenant record in landlord database
- Sets up domain and database configuration
- Handles existing tenant conflicts (with --force)

### Step 2: Customer & Brand Generation
- Creates a customer record with fake company data
- Generates realistic brand information including:
  - Company name (from predefined list)
  - Email address
  - Phone number
  - Business address
  - Website URL

### Step 3: Subscription Setup
- Creates subscription linking customer, brand, and plan
- Enables specified modules for the brand
- Sets up active subscription status

### Step 4: Database Setup
- Runs all tenant migrations
- Seeds initial data
- Creates required tables for enabled modules
- Sets up Laravel Passport (OAuth2 keys and personal access client)

### Step 5: Nginx Configuration
- Generates nginx configuration file
- Places it in `/etc/nginx/sites-available/`
- Creates symlink in `/etc/nginx/sites-enabled/`
- Includes security headers and optimization

### Step 6: Service Management
- Tests nginx configuration
- Restarts nginx service
- Provides status feedback

## Generated Files

### Nginx Configuration
Location: `/etc/nginx/sites-available/{domain}`

```nginx
server {
    listen 80;
    server_name {domain};
    root {project_path}/public;
    index index.php index.html index.htm;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline'" always;

    # Gzip compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_proxied expired no-cache no-store private must-revalidate auth;
    gzip_types text/plain text/css text/xml text/javascript application/x-javascript application/xml+rss;
}
```

## Fake Brand Names

The command uses a predefined list of realistic company names:

- TechCorp Solutions
- InnovateLab Inc
- Global Enterprises
- Digital Dynamics
- CloudTech Systems
- NextGen Solutions
- SmartWorks Corp
- FutureTech Ltd
- ProActive Systems
- Elite Solutions
- Advanced Technologies
- Prime Systems
- Ultimate Solutions
- MegaCorp Industries
- SuperTech Enterprises
- Alpha Systems
- Beta Technologies
- Gamma Solutions
- Delta Systems
- Omega Technologies

## Output Example

```
🚀 Starting Tenant Generation Process...

📋 Generation Plan:
   Tenant Name: mycompany
   Brand Name: TechCorp Solutions
   Domain: mycompany.saas.test
   Database: saas_mycompany
   Modules: HR, CRM, Ticket

🏗️  Creating tenant...
   ✅ Tenant created: mycompany (ID: 12)

👤 Creating customer...
   ✅ Customer created: TechCorp Solutions (ID: 8)

🏢 Creating brand...
   ✅ Brand created: TechCorp Solutions (ID: 5)

📦 Creating subscription with modules...
   ✅ Module enabled: HR
   ✅ Module enabled: CRM
   ✅ Module enabled: Ticket
   ✅ Subscription created with 3 modules

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
   Modules: HR, CRM, Ticket

🔧 Next Steps:
   1. Update your /etc/hosts file: 127.0.0.1 mycompany.saas.test
   2. Access the tenant at: http://mycompany.saas.test
   3. Create a super admin user: php artisan tenant:create-super-admin mycompany
```

## Post-Generation Steps

### 1. Update Hosts File
Add the domain to your `/etc/hosts` file:
```bash
echo "127.0.0.1 mycompany.saas.test" | sudo tee -a /etc/hosts
```

### 2. Create Super Admin User
```bash
php artisan tenant:create-super-admin mycompany \
  --name="Super Admin" \
  --email="admin@mycompany.local" \
  --username="admin" \
  --password="password123"
```

### 3. Access the Tenant
Visit: `http://mycompany.saas.test`

## Troubleshooting

### Common Issues

#### 1. Permission Denied for Nginx
**Error**: Cannot write to `/etc/nginx/sites-available/`

**Solution**:
```bash
sudo chown -R $USER:$USER /etc/nginx/sites-available/
sudo chown -R $USER:$USER /etc/nginx/sites-enabled/
```

#### 2. Nginx Configuration Test Failed
**Error**: `nginx: configuration file test failed`

**Solution**:
```bash
sudo nginx -t
# Fix any syntax errors in the generated config
sudo systemctl restart nginx
```

#### 3. Database Already Exists
**Error**: `Database 'saas_mycompany' already exists`

**Solution**:
```bash
# Use --force flag to overwrite
php artisan tenant:generate --name=mycompany --modules=hr,crm --force
```

#### 4. Module Not Found
**Error**: `Module 'invalidmodule' not found`

**Solution**:
Check available modules and use correct module keys:
```bash
# Available modules: hr, crm, ticket, accounting, inventory, sales, reporting, email, notification, filemanager, utilities, geography, localization, payment, subscription, development, customer, tenant, auth, api, comment, workflow, staticpages, monitoring
```

### Debug Commands

#### Check Generated Tenant
```bash
php artisan tinker --execute="
\$tenant = \Modules\Tenant\Entities\Tenant::where('name', 'mycompany')->first();
if(\$tenant) {
    echo 'Tenant: ' . \$tenant->name . PHP_EOL;
    echo 'Domain: ' . \$tenant->domain . PHP_EOL;
    echo 'Database: ' . \$tenant->database . PHP_EOL;
} else {
    echo 'Tenant not found' . PHP_EOL;
}
"
```

#### Check Enabled Modules
```bash
php artisan tinker --execute="
\$brand = \Modules\Customer\Entities\Brand::where('name', 'LIKE', '%TechCorp%')->first();
if(\$brand) {
    echo 'Brand: ' . \$brand->name . PHP_EOL;
    echo 'Enabled modules: ' . \$brand->modules->pluck('name')->implode(', ') . PHP_EOL;
} else {
    echo 'Brand not found' . PHP_EOL;
}
"
```

#### Verify Nginx Configuration
```bash
# Check if config exists
ls -la /etc/nginx/sites-available/mycompany.saas.test

# Check if symlink exists
ls -la /etc/nginx/sites-enabled/mycompany.saas.test

# Test nginx config
sudo nginx -t
```

## Security Considerations

1. **File Permissions**: Ensure nginx config files have proper permissions
2. **Domain Validation**: Validate domain names to prevent injection
3. **Database Security**: Use strong database names and passwords
4. **Nginx Security**: Generated config includes security headers
5. **Access Control**: Limit who can run this command

## Performance Notes

1. **Database Creation**: Large tenant databases may take time to create
2. **Migration Time**: Complex modules may require longer migration times
3. **Nginx Restart**: Service restart may cause brief downtime
4. **Concurrent Creation**: Avoid creating multiple tenants simultaneously

---

*This command is designed for development and testing environments. For production use, additional security and validation measures should be implemented.*


