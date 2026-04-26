<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Modules\Tenant\Entities\Tenant;
use Modules\Customer\Entities\Brand;
use Modules\Customer\Entities\Customer;
use Modules\Subscription\Entities\Plan;
use Modules\Subscription\Entities\Subscription;
use Modules\Utilities\Entities\Module;
use Illuminate\Support\Str;

class TenantGenerateCommand extends Command
{
    protected $signature = 'tenant:generate
                            {--name= : Tenant name}
                            {--modules= : Comma-separated list of modules (e.g., hr,crm,ticket)}
                            {--domain= : Custom domain (optional)}
                            {--database= : Custom database name (optional)}
                            {--plan= : Plan name to assign (default: Free Plan)}
                            {--trial-days=14 : Number of trial days (default: 14)}
                            {--force : Force creation even if tenant exists}';

    protected $description = 'Generate a new tenant with specified modules, fake brand, and nginx configuration';

    private $availableModules = [
        'hr' => 'HR',
        'crm' => 'CRM',
        'ticket' => 'Ticket',
        'accounting' => 'Accounting',
        'inventory' => 'Inventory',
        'sales' => 'Sales',
        'reporting' => 'Reporting',
        'email' => 'Email',
        'notification' => 'Notification',
        'filemanager' => 'FileManager',
        'utilities' => 'Utilities',
        'geography' => 'Geography',
        'localization' => 'Localization',
        'payment' => 'Payment',
        'subscription' => 'Subscription',
        'development' => 'Development',
        'customer' => 'Customer',
        'tenant' => 'Tenant',
        'auth' => 'Auth',
        'api' => 'API',
        'comment' => 'Comment',
        'workflow' => 'Workflow',
        'staticpages' => 'StaticPages',
        'monitoring' => 'Monitoring'
    ];

    private $fakeBrandNames = [
        'TechCorp Solutions',
        'InnovateLab Inc',
        'Global Enterprises',
        'Digital Dynamics',
        'CloudTech Systems',
        'NextGen Solutions',
        'SmartWorks Corp',
        'FutureTech Ltd',
        'ProActive Systems',
        'Elite Solutions',
        'Advanced Technologies',
        'Prime Systems',
        'Ultimate Solutions',
        'MegaCorp Industries',
        'SuperTech Enterprises',
        'Alpha Systems',
        'Beta Technologies',
        'Gamma Solutions',
        'Delta Systems',
        'Omega Technologies'
    ];

    public function handle()
    {
        $this->info('🚀 Starting Tenant Generation Process...');
        $this->newLine();

        // Validate inputs
        $tenantName = $this->option('name');
        $modulesInput = $this->option('modules');
        $customDomain = $this->option('domain');
        $customDatabase = $this->option('database');
        $planName = $this->option('plan') ?: 'Free Plan';
        $trialDays = (int) $this->option('trial-days');
        $force = $this->option('force');

        if (!$tenantName) {
            $tenantName = $this->ask('Enter tenant name');
        }

        if (!$modulesInput) {
            $modulesInput = $this->ask('Enter modules (comma-separated)', 'hr,crm,ticket');
        }

        // Parse modules
        $requestedModules = array_map('trim', explode(',', $modulesInput));
        $validModules = $this->validateModules($requestedModules);

        if (empty($validModules)) {
            $this->error('❌ No valid modules provided. Available modules: ' . implode(', ', array_keys($this->availableModules)));
            return 1;
        }

        // Generate fake brand name
        $brandName = $this->generateFakeBrandName();

        // Generate domain and database names
        $domain = $customDomain ?: $this->generateDomain($tenantName);
        $database = $customDatabase ?: $this->generateDatabaseName($tenantName);

        $this->info("📋 Generation Plan:");
        $this->line("   Tenant Name: {$tenantName}");
        $this->line("   Brand Name: {$brandName}");
        $this->line("   Domain: {$domain}");
        $this->line("   Database: {$database}");
        $this->line("   Modules: " . implode(', ', $validModules));
        $this->line("   Plan: {$planName}");
        $this->line("   Trial Days: {$trialDays}");
        $this->newLine();

        if (!$force && Tenant::where('name', $tenantName)->exists()) {
            $this->error("❌ Tenant '{$tenantName}' already exists. Use --force to overwrite.");
            return 1;
        }

        try {
            // Step 1: Create Tenant
            $tenant = $this->createTenant($tenantName, $domain, $database, $force);

            // Step 2: Create Customer and Brand
            $customer = $this->createCustomer($brandName, $tenant->id);
            $brand = $this->createBrand($customer, $brandName, $tenant->id);

            // Step 3: Setup Tenant Database
            $this->setupTenantDatabase($tenantName);

            // Step 4: Create Subscription and assign plan
            $this->createSubscription($customer, $brand, $validModules, $planName, $trialDays);

            // Step 5: Setup Passport for Tenant
            $this->setupPassportForTenant($tenantName);

            // Step 6: Generate Nginx Configuration
            $this->generateNginxConfig($domain);

            // Step 7: Restart Nginx
            $this->restartNginx();

            $this->newLine();
            $this->info('🎉 Tenant generation completed successfully!');
            $this->newLine();
            $this->info('📋 Summary:');
            $this->line("   Tenant: {$tenantName}");
            $this->line("   Domain: http://{$domain}");
            $this->line("   Database: {$database}");
            $this->line("   Brand: {$brandName}");
            $this->line("   Modules: " . implode(', ', $validModules));
            $this->line("   Plan: {$planName}");
            $this->newLine();
            $this->info('🔧 Next Steps:');
            $this->line('   1. Update your /etc/hosts file: 127.0.0.1 ' . $domain);
            $this->line('   2. Access the tenant at: http://' . $domain);
            $this->line('   3. Create a super admin user: php artisan tenant:create-super-admin ' . $tenantName);

            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Tenant generation failed: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            return 1;
        }
    }

    private function validateModules(array $modules): array
    {
        $validModules = [];
        foreach ($modules as $module) {
            $moduleKey = strtolower($module);
            if (isset($this->availableModules[$moduleKey])) {
                $validModules[] = $this->availableModules[$moduleKey];
            } else {
                $this->warn("⚠️  Module '{$module}' not found. Skipping...");
            }
        }
        return $validModules;
    }

    private function generateFakeBrandName(): string
    {
        return $this->fakeBrandNames[array_rand($this->fakeBrandNames)];
    }

    private function generateDomain(string $tenantName): string
    {
        return strtolower($tenantName) . '.saas.test';
    }

    private function generateDatabaseName(string $tenantName): string
    {
        return 'saas_' . strtolower(str_replace([' ', '-'], '_', $tenantName));
    }

    private function createTenant(string $name, string $domain, string $database, bool $force): Tenant
    {
        $this->info('🏗️  Creating tenant...');

        $tenantData = [
            'name' => $name,
            'domain' => $domain,
            'database' => $database,
        ];

        if ($force) {
            $tenant = Tenant::updateOrCreate(['name' => $name], $tenantData);
        } else {
            $tenant = Tenant::create($tenantData);
        }

        $this->line("   ✅ Tenant created: {$tenant->name} (ID: {$tenant->id})");
        return $tenant;
    }

    private function createCustomer(string $brandName, int $tenantId): Customer
    {
        $this->info('👤 Creating customer...');

        // Get a default category
        $category = \Modules\Utilities\Entities\Category::first();
        if (!$category) {
            // Create a default category if none exists
            $category = \Modules\Utilities\Entities\Category::create([
                'name' => 'Business',
                'slug' => 'business',
                'description' => 'General business category',
                'is_active' => true,
            ]);
        }

        $username = strtolower(str_replace([' ', '.', '-'], ['_', '', ''], $brandName)) . '_' . rand(100, 999);

        $customer = Customer::create([
            'name' => $brandName,
            'username' => $username,
            'tenant_id' => $tenantId,
            'category_id' => $category->id,
        ]);

        $this->line("   ✅ Customer created: {$customer->name} (ID: {$customer->id})");
        return $customer;
    }

    private function createBrand(Customer $customer, string $brandName, int $tenantId): Brand
    {
        $this->info('🏢 Creating brand...');

        $brand = Brand::create([
            'name' => $brandName,
            'slug' => Str::slug($brandName),
            'description' => 'Automated brand for ' . $brandName,
            'tenant_id' => $tenantId,
            'created_by' => 1, // Default user ID
        ]);

        $this->line("   ✅ Brand created: {$brand->name} (ID: {$brand->id})");
        return $brand;
    }

    private function createSubscription(Customer $customer, Brand $brand, array $modules, string $planName, int $trialDays): void
    {
        $this->info('📦 Creating subscription with modules...');

        // Get the specified plan or default to Free Plan
        $plan = Plan::where('name', $planName)->first();
        if (!$plan) {
            $this->warn("   ⚠️  Plan '{$planName}' not found. Trying 'Free Plan'...");
            $plan = Plan::where('name', 'Free Plan')->first();

            if (!$plan) {
                $this->warn("   ⚠️  Free Plan not found. Using first available plan.");
                $plan = Plan::first();
            }
        }

        if (!$plan) {
            $this->error("   ❌ No plans found in database. Please seed plans first.");
            return;
        }

        $this->line("   📋 Using plan: {$plan->name} (ID: {$plan->id})");

        // Determine subscription status and dates based on trial
        $isFreePlan = stripos($plan->name, 'free') !== false;
        $status = $isFreePlan ? 'active' : 'trial';
        $startDate = now();
        $endDate = $isFreePlan ? now()->addYear(100) : now()->addDays($trialDays);

        // Create subscription
        $subscription = Subscription::create([
            'customer_id' => $customer->id,
            'brand_id' => $brand->id,
            'plan_id' => $plan->id,
            'status' => $status,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'trial_ends_at' => $isFreePlan ? null : $endDate,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->line("   ✅ Subscription created: {$status} (ends: {$endDate->format('Y-m-d')})");

        // Enable modules for the brand
        $enabledCount = 0;
        foreach ($modules as $moduleName) {
            $module = Module::where('name', $moduleName)->first();
            if ($module) {
                $brand->modules()->syncWithoutDetaching([$module->id]);
                $this->line("   ✅ Module enabled: {$moduleName}");
                $enabledCount++;
            } else {
                $this->warn("   ⚠️  Module not found: {$moduleName}");
            }
        }

        $this->line("   ✅ Subscription created with {$enabledCount} modules enabled");
    }

    private function setupTenantDatabase(string $tenantName): void
    {
        $this->info('🗄️  Setting up tenant database...');

        // Get tenant
        $tenant = Tenant::where('name', $tenantName)->first();
        if (!$tenant) {
            throw new \Exception("Tenant '{$tenantName}' not found");
        }

        // Run migrations
        $this->line("   Running migrations...");
        Artisan::call('tenant:setup', [
            'tenant' => $tenant->id,
            '--force' => true
        ]);

        $this->line("   ✅ Database setup completed");
    }

    private function setupPassportForTenant(string $tenantName): void
    {
        $this->info('🔑 Setting up Passport for tenant...');

        // Get tenant
        $tenant = Tenant::where('name', $tenantName)->first();
        if (!$tenant) {
            throw new \Exception("Tenant '{$tenantName}' not found");
        }

        try {
            $this->line("   Running Passport setup seeder...");

            $command = "tenants:artisan 'db:seed --class=Database\\Seeders\\Tenant\\PassportSetupSeeder --database=tenant --force' --tenant={$tenant->id}";
            Artisan::call($command);

            $output = Artisan::output();
            if (trim($output)) {
                $this->line("   ✅ Passport setup completed for tenant: {$tenantName}");
            } else {
                $this->line("   ⏭️  Passport setup skipped or already configured");
            }
        } catch (\Exception $e) {
            $this->warn("   ⚠️  Warning: Passport setup failed - {$e->getMessage()}");
            $this->line("   Manual Passport setup may be required for tenant: {$tenantName}");
        }
    }

    private function generateNginxConfig(string $domain): void
    {
        $this->info('🌐 Generating nginx configuration...');

        $nginxConfig = $this->generateNginxConfigContent($domain);
        $configPath = "/etc/nginx/sites-available/{$domain}";
        $enabledPath = "/etc/nginx/sites-enabled/{$domain}";

        try {
            // Write nginx configuration
            File::put($configPath, $nginxConfig);
            $this->line("   ✅ Nginx config written to: {$configPath}");

            // Create symlink to sites-enabled
            if (!File::exists($enabledPath)) {
                Process::run("sudo ln -s {$configPath} {$enabledPath}");
                $this->line("   ✅ Symlink created: {$enabledPath}");
            }

        } catch (\Exception $e) {
            $this->warn("   ⚠️  Could not write nginx config: " . $e->getMessage());
            $this->line("   Manual nginx config needed for domain: {$domain}");
        }
    }

    private function generateNginxConfigContent(string $domain): string
    {
        $projectPath = base_path();

        return <<<NGINX
server {
    listen 80;
    server_name {$domain};
    root {$projectPath}/public;
    index index.php index.html index.htm;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
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
NGINX;
    }

    private function restartNginx(): void
    {
        $this->info('🔄 Restarting nginx...');

        try {
            // Test nginx configuration
            $testResult = Process::run('sudo nginx -t');
            if ($testResult->successful()) {
                // Restart nginx
                Process::run('sudo systemctl restart nginx');
                $this->line("   ✅ Nginx restarted successfully");
            } else {
                $this->warn("   ⚠️  Nginx configuration test failed:");
                $this->line("   " . $testResult->errorOutput());
            }
        } catch (\Exception $e) {
            $this->warn("   ⚠️  Could not restart nginx: " . $e->getMessage());
            $this->line("   Please restart nginx manually: sudo systemctl restart nginx");
        }
    }
}
