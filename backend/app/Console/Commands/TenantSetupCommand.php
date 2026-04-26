<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;
use Modules\Tenant\Entities\Tenant;
use Modules\Customer\Entities\Customer;
use Modules\Customer\Entities\Brand;
use Modules\Subscription\Entities\Plan;
use Modules\Subscription\Entities\Subscription;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class TenantSetupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:setup
                            {tenant? : Tenant ID or name to setup (defaults to all tenants)}
                            {--force : Force the operation without confirmation}
                            {--migrate-only : Run migrations only, skip seeding}
                            {--seed-only : Run seeding only, skip migrations}
                            {--fresh : Run fresh migrations (drop all tables and re-run migrations)}
                            {--plan= : Plan name to assign (default: Free Plan)}
                            {--trial-days=14 : Number of trial days (default: 14)}
                            {--skip-subscription : Skip subscription creation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Complete tenant database setup: run migrations and seed all necessary data (excluding landlord)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->displayHeader();

        $tenantInput = $this->argument('tenant');
        $planName = $this->option('plan') ?: 'Free Plan';
        $trialDays = (int) $this->option('trial-days');
        $skipSubscription = $this->option('skip-subscription');
        $tenants = $this->getTenants($tenantInput);

        if ($tenants->isEmpty() && $tenantInput) {
            // Tenant doesn't exist, create it
            $this->displaySection('Creating New Tenant');
            $this->info("   📝 Tenant '{$tenantInput}' not found. Creating...");
            $tenant = $this->createTenant($tenantInput);
            $tenants = collect([$tenant]);
        }

        if ($tenants->isEmpty()) {
            $this->error('No tenants found (excluding landlord). Please provide a tenant name or ID.');
            return 1;
        }

        foreach ($tenants as $tenant) {
            $this->displayTenantInfo($tenant);
            $this->newLine();

            // Ensure the tenant database exists before running migrations
            $this->ensureDatabaseExists($tenant);

            // Check if we should run migrations
            if (!$this->option('seed-only')) {
                $this->runTenantMigrations($tenant);
            }

            // Check if we should run seeding
            if (!$this->option('migrate-only')) {
                $this->runTenantSeeding($tenant);
            }

            // Create subscription if not skipped
            if (!$skipSubscription) {
                $this->createSubscriptionForTenant($tenant, $planName, $trialDays);
            }

            // Setup Passport for tenant
            $this->setupPassportForTenant($tenant);

            // Generate nginx config for local environment
            if (app()->environment('local')) {
                $this->generateNginxConfig($tenant);
            }

            $this->newLine();
            $this->info("✅ Tenant '{$tenant->name}' setup completed successfully!");
            $this->newLine();
        }

        $this->displaySuccess('All tenant setups completed successfully!');
        return 0;
    }

    /**
     * Display formatted header
     */
    private function displayHeader()
    {
        $this->newLine();
        $this->line('<fg=white;bg=blue> 🏗️  Tenant Database Setup </>');
        $this->newLine();
    }

    /**
     * Display section header
     */
    private function displaySection(string $title)
    {
        $this->newLine();
        $this->line("<fg=cyan>━━━ {$title} ━━━</>");
        $this->newLine();
    }

    /**
     * Display tenant information
     */
    private function displayTenantInfo($tenant)
    {
        $this->displaySection("Setting Up Tenant: {$tenant->name}");
        $this->line("   <fg=green>ID:</>        {$tenant->id}");
        $this->line("   <fg=green>Database:</>  {$tenant->database}");
        $this->line("   <fg=green>Domain:</>    {$tenant->domain}");
    }

    /**
     * Display success message
     */
    private function displaySuccess(string $message)
    {
        $this->line("<fg=green;options=bold>✅ {$message}</>");
    }

    /**
     * Display warning message
     */
    private function displayWarning(string $message)
    {
        $this->line("<fg=yellow>⚠️  {$message}</>");
    }

    /**
     * Display error message
     */
    private function displayError(string $message)
    {
        $this->line("<fg=red>❌ {$message}</>");
    }

    /**
     * Get tenants to setup (excluding landlord)
     */
    private function getTenants($tenantInput)
    {
        if ($tenantInput) {
            // Try to find by ID first, then by name (excluding landlord)
            $tenant = Tenant::where('domain', '!=', 'landlord')
                ->where(function($query) use ($tenantInput) {
                    $query->where('id', $tenantInput)
                          ->orWhere('name', $tenantInput);
                })
                ->first();

            if ($tenant) {
                return collect([$tenant]);
            } else {
                $this->error("Tenant '{$tenantInput}' not found (excluding landlord).");
                return collect();
            }
        }

        // Return all tenants except landlord
        return Tenant::where('domain', '!=', 'landlord')->get();
    }

    /**
     * Run all tenant migrations with progress bar
     */
    private function runTenantMigrations($tenant)
    {
        $this->info('📦 Running Tenant Migrations...');

        $migrationPaths = [
            'database/migrations/shared',
            'database/migrations/tenant',
        ];

        // Dynamically find all module migration paths
        $moduleBasePath = base_path('modules');
        if (is_dir($moduleBasePath)) {
            $modules = glob($moduleBasePath . '/*/database/migrations/tenant', GLOB_ONLYDIR);
            foreach ($modules as $modulePath) {
                $relativePath = str_replace(base_path() . '/', '', $modulePath);
                $migrationPaths[] = $relativePath;
            }

            $modules = glob($moduleBasePath . '/*/database/migrations/shared', GLOB_ONLYDIR);
            foreach ($modules as $modulePath) {
                $relativePath = str_replace(base_path() . '/', '', $modulePath);
                $migrationPaths[] = $relativePath;
            }
        }

        // Count migration files
        $totalMigrations = 0;
        foreach ($migrationPaths as $path) {
            $absolutePath = base_path($path);
            if (is_dir($absolutePath)) {
                $totalMigrations += count(glob($absolutePath . '/*.php'));
            }
        }

        if ($totalMigrations === 0) {
            $this->line('   ⏭️  No migrations found');
            $this->newLine();
            return;
        }

        $isFresh = $this->option('fresh');

        // Create progress bar
        $bar = $this->output->createProgressBar($totalMigrations);
        $bar->setFormat('      %current%/%max% [%bar%] %percent:3s%% -- %message%');
        $bar->setMessage('Starting migrations...');
        $bar->start();

        try {
            // Run migrations using tenant context with all paths combined
            $tenant->execute(function () use ($isFresh, $migrationPaths, $bar) {
                if (!$isFresh) {
                    $this->markExistingCreateTableMigrationsAsRan($migrationPaths);
                }

                $params = [
                    '--database' => 'tenant',
                    '--force' => true,
                    '--path' => $migrationPaths,
                ];

                if ($isFresh) {
                    Artisan::call('migrate:fresh', $params);
                } else {
                    Artisan::call('migrate', $params);
                }

                $output = Artisan::output();
                foreach (explode("\n", trim($output)) as $line) {
                    if (preg_match('/^\d{4}_\d{2}_\d{2}_\d+_\S+/', $line)) {
                        $bar->setMessage('✓ ' . trim($line));
                        $bar->advance();
                    }
                }
            });

            // Finish progress bar
            if ($bar->getProgress() < $bar->getMaxSteps()) {
                $bar->finish();
            }
            $bar->setMessage('Migrations completed ✅');
            $bar->finish();

            $this->newLine();
            $this->line('   ✅ All tenant migrations completed successfully');
        } catch (\Exception $e) {
            $bar->finish();
            $this->newLine();
            $this->error('   ❌ Migration failed: ' . $e->getMessage());
            throw $e;
        }

        $this->newLine();
    }

    /**
     * Mark pending create-table migrations as ran when tables already exist.
     *
     * This makes tenant setup idempotent for existing databases where some tables
     * were created previously but their migration rows are missing.
     */
    private function markExistingCreateTableMigrationsAsRan(array $migrationPaths): void
    {
        // Ensure the migrations table exists before we read or insert rows.
        if (!Schema::connection('tenant')->hasTable('migrations')) {
            Artisan::call('migrate:install', [
                '--database' => 'tenant',
            ]);
        }

        $existingMigrations = DB::connection('tenant')
            ->table('migrations')
            ->pluck('migration')
            ->flip();

        $currentBatch = (int) DB::connection('tenant')
            ->table('migrations')
            ->max('batch');

        $insertBatch = $currentBatch > 0 ? $currentBatch + 1 : 1;
        $migrationsToInsert = [];

        foreach ($migrationPaths as $relativePath) {
            $absolutePath = base_path($relativePath);
            if (!is_dir($absolutePath)) {
                continue;
            }

            $migrationFiles = glob($absolutePath . '/*.php') ?: [];

            foreach ($migrationFiles as $migrationFile) {
                $migrationName = basename($migrationFile, '.php');

                if ($existingMigrations->has($migrationName)) {
                    continue;
                }

                if (!preg_match('/create_([a-z0-9_]+)_table$/i', $migrationName, $matches)) {
                    continue;
                }

                $tableName = $matches[1];
                if (!Schema::connection('tenant')->hasTable($tableName)) {
                    continue;
                }

                $migrationsToInsert[] = [
                    'migration' => $migrationName,
                    'batch' => $insertBatch,
                ];
            }
        }

        if (!empty($migrationsToInsert)) {
            DB::connection('tenant')->table('migrations')->insert($migrationsToInsert);
        }
    }

    /**
     * Run tenant seeding with progress bar
     */
    private function runTenantSeeding($tenant)
    {
        $this->info('🌱 Running Tenant Seeding...');

        $seederPaths = [
            'database/seeders/tenant',
        ];

        // Dynamically find all module seeder paths
        $moduleBasePath = base_path('modules');
        if (is_dir($moduleBasePath)) {
            $modules = glob($moduleBasePath . '/*/database/seeders/tenant', GLOB_ONLYDIR);
            foreach ($modules as $modulePath) {
                $relativePath = str_replace(base_path() . '/', '', $modulePath);
                $seederPaths[] = $relativePath;
            }

            $modules = glob($moduleBasePath . '/*/database/seeders/shared', GLOB_ONLYDIR);
            foreach ($modules as $modulePath) {
                $relativePath = str_replace(base_path() . '/', '', $modulePath);
                $seederPaths[] = $relativePath;
            }
        }

        // Count seeder files
        $totalSeeders = 0;
        foreach ($seederPaths as $path) {
            $absolutePath = base_path($path);
            if (is_dir($absolutePath)) {
                $totalSeeders += count(glob($absolutePath . '/*Seeder.php'));
            }
        }

        if ($totalSeeders === 0) {
            $this->line('   ⏭️  No seeders found');
            $this->newLine();
            return;
        }

        // Create progress bar
        $bar = $this->output->createProgressBar($totalSeeders);
        $bar->setFormat('      %current%/%max% [%bar%] %percent:3s%% -- %message%');
        $bar->setMessage('Starting seeders...');
        $bar->start();

        foreach ($seederPaths as $path) {
            try {
                $this->runTenantSeedersFromPath($path, $tenant, $bar);
            } catch (\Exception $e) {
                $this->warn("   ⚠️  Warning: {$path} - {$e->getMessage()}");
            }
        }

        $bar->setMessage('Seeding completed ✅');
        $bar->finish();
        $this->newLine();
        $this->line('   ✅ All tenant seeders completed successfully');
        $this->newLine();
    }

    /**
     * Get force flag for commands
     */
    private function getForceFlag()
    {
        return $this->option('force') ? ' --force' : '';
    }

    /**
     * Run all tenant seeders from a specific path with progress bar
     */
    private function runTenantSeedersFromPath($path, $tenant, $bar)
    {
        if (!is_dir(base_path($path))) {
            return;
        }

        $seederFiles = glob(base_path($path) . '/*Seeder.php');

        foreach ($seederFiles as $seederFile) {
            $fileName = basename($seederFile, '.php');

            // Convert file path to class name
            $className = $this->getTenantSeederClassName($seederFile, $path);

            if ($className) {
                try {
                    $bar->setMessage('Seeding: ' . $fileName);

                    $tenant->execute(function () use ($className) {
                        Artisan::call('db:seed', [
                            '--class' => $className,
                            '--database' => 'tenant',
                            '--force' => true,
                        ]);
                    });

                    $bar->advance();
                } catch (\Exception $e) {
                    $bar->setMessage('⚠️  ' . $fileName . ' failed');
                    $bar->advance();
                }
            }
        }
    }

    /**
     * Get tenant seeder class name from file path
     */
    private function getTenantSeederClassName($filePath, $basePath)
    {
        if (!file_exists($filePath)) {
            return null;
        }

        // Read the namespace directly from the file to avoid casing mismatches
        $contents = file_get_contents($filePath);

        if (preg_match('/^namespace\s+([a-zA-Z0-9_\\\\]+);/m', $contents, $matches)) {
            $namespace = $matches[1];
            $className = basename($filePath, '.php');
            return $namespace . '\\' . $className;
        }

        return null;
    }

    /**
     * Ensure the tenant database exists before running migrations
     */
    private function ensureDatabaseExists($tenant)
    {
        $dbName = $tenant->database;

        if (!$dbName) {
            $this->warn('   ⚠️  No database name configured for tenant. Skipping database creation.');
            return;
        }

        try {
            $exists = DB::connection('landlord')
                ->select("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?", [$dbName]);

            if (empty($exists)) {
                DB::connection('landlord')->statement("CREATE DATABASE IF NOT EXISTS `{$dbName}`");
                $this->line("   ✅ Created database: {$dbName}");
            } else {
                $this->line("   ✓ Database already exists: {$dbName}");
            }
        } catch (\Exception $e) {
            $this->warn("   ⚠️  Warning: Could not verify/create database '{$dbName}' - {$e->getMessage()}");
        }
    }

    /**
     * Setup Passport for tenant
     */
    private function setupPassportForTenant($tenant)
    {
        $this->info('🔑 Setting up Passport for tenant...');

        try {
            $this->line("   Running Passport setup seeder...");

            // Use dynamic class name resolution
            $passportSeederPath = base_path('database/seeders/tenant/PassportSetupSeeder.php');
            $className = $this->getTenantSeederClassName($passportSeederPath, 'database/seeders/tenant');

            if ($className) {
                $exitCode = null;
                $output = '';

                $tenant->execute(function () use ($className, &$exitCode, &$output) {
                    $exitCode = Artisan::call('db:seed', [
                        '--class' => $className,
                        '--database' => 'tenant',
                        '--force' => true,
                    ]);
                    $output = Artisan::output();
                });

                // Display seeder output
                if (trim($output)) {
                    foreach (explode("\n", trim($output)) as $line) {
                        if ($line) {
                            $this->line("      " . $line);
                        }
                    }
                }

                if ($exitCode === 0) {
                    $this->line("   ✅ Passport setup completed for tenant: {$tenant->name}");
                } else {
                    $this->warn("   ⚠️  Passport setup exited with code: {$exitCode}");
                }
            } else {
                $this->warn("   ⚠️  Warning: Could not resolve PassportSetupSeeder class name");
            }
        } catch (\Exception $e) {
            $this->warn("   ⚠️  Warning: Passport setup failed - {$e->getMessage()}");
            $this->line("   Manual Passport setup may be required: php artisan tenant:setup {$tenant->id}");
        }

        $this->newLine();
    }

    /**
     * Create or ensure subscription exists for tenant
     */
    private function createSubscriptionForTenant($tenant, string $planName, int $trialDays)
    {
        $this->info('📦 Checking subscription for tenant...');

        // Check if tenant already has a customer, create if not
        $customer = Customer::where('tenant_id', $tenant->id)->first();
        if (!$customer) {
            $this->info('   👤 Creating customer...');

            // Get a default category
            $category = \Modules\Utilities\Entities\Category::first();
            if (!$category) {
                $category = \Modules\Utilities\Entities\Category::create([
                    'name' => 'Business',
                    'slug' => 'business',
                    'description' => 'General business category',
                    'is_active' => true,
                ]);
            }

            $customer = Customer::create([
                'name' => $tenant->name,
                'username' => strtolower(str_replace([' ', '.', '-'], ['_', '', ''], $tenant->name)) . '_' . rand(100, 999),
                'tenant_id' => $tenant->id,
                'category_id' => $category->id,
            ]);

            $this->line("   ✅ Customer created: {$customer->name} (ID: {$customer->id})");
        }

        // Check if customer already has a brand, create if not
        $brand = Brand::where('tenant_id', $tenant->id)->first();
        if (!$brand) {
            $this->info('   🏢 Creating brand...');

            $brandName = ucfirst($tenant->name) . ' Brand';

            $brand = Brand::create([
                'name' => $brandName,
                'slug' => Str::slug($brandName),
                'description' => 'Default brand for ' . $tenant->name,
                'tenant_id' => $tenant->id,
                'created_by' => 1, // Default user ID
            ]);

            $this->line("   ✅ Brand created: {$brand->name} (ID: {$brand->id})");
        }

        // Check if subscription already exists
        $existingSubscription = Subscription::where('brand_id', $brand->id)
            ->where('tenant_id', $tenant->id)
            ->first();

        if ($existingSubscription) {
            $this->line("   ✓ Subscription already exists: {$existingSubscription->status}");
            return;
        }

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

        // Create subscription with landlord admin user (ID 1) as default
        // The user_id will be updated when a tenant user is created later
        $subscription = Subscription::create([
            'tenant_id' => $tenant->id,
            'brand_id' => $brand->id,
            'user_id' => 1, // Landlord admin user
            'plan_id' => $plan->id,
            'status' => $status,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'price' => $plan->price ?? 0,
            'currency_id' => 1, // Default currency ID (USD)
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->line("   ✅ Subscription created: {$status} (ends: {$endDate->format('Y-m-d')})");
        $this->newLine();
    }

    /**
     * Create a new tenant
     */
    private function createTenant(string $tenantName)
    {
        $this->info('🏗️  Creating tenant...');

        // Generate domain and database names
        $domain = strtolower($tenantName) . '.saas.test';
        $database = 'saas_' . strtolower(str_replace([' ', '-'], '_', $tenantName));

        $tenantData = [
            'name' => $tenantName,
            'domain' => $domain,
            'database' => $database,
        ];

        $tenant = Tenant::create($tenantData);

        $this->line("   ✅ Tenant created: {$tenant->name} (ID: {$tenant->id})");
        $this->line("   Domain: {$domain}");
        $this->line("   Database: {$database}");
        $this->newLine();

        // Create the database
        $this->ensureDatabaseExists($tenant);

        return $tenant;
    }

    /**
     * Generate nginx configuration for tenant (local environment only)
     */
    private function generateNginxConfig($tenant)
    {
        $this->info('🌐 Generating nginx configuration...');

        $templatePath = base_path('../docs/nginx/tenant-config.conf');
        $configPath = "/etc/nginx/sites-available/{$tenant->domain}";
        $enabledPath = "/etc/nginx/sites-enabled/{$tenant->domain}";

        if (!File::exists($templatePath)) {
            $this->warn("   ⚠️  Nginx template not found at: {$templatePath}");
            return;
        }

        $projectPath = base_path();
        $templateContent = File::get($templatePath);

        // Replace placeholders
        $nginxConfig = str_replace(
            ['{DOMAIN}', '{PROJECT_PATH}'],
            [$tenant->domain, $projectPath],
            $templateContent
        );

        try {
            // Write nginx configuration
            File::put($configPath, $nginxConfig);
            $this->line("   ✅ Nginx config written to: {$configPath}");

            // Create symlink to sites-enabled
            if (!File::exists($enabledPath)) {
                shell_exec("sudo ln -s {$configPath} {$enabledPath}");
                $this->line("   ✅ Symlink created: {$enabledPath}");
            }

            // Test and reload nginx
            $testResult = shell_exec('sudo nginx -t 2>&1');
            if (str_contains($testResult, 'successful') || str_contains($testResult, 'syntax is ok')) {
                shell_exec('sudo systemctl reload nginx');
                $this->line("   ✅ Nginx reloaded successfully");
            } else {
                $this->warn("   ⚠️  Nginx configuration test failed:");
                $this->line("   " . trim($testResult));
            }

            // Add to /etc/hosts if not present
            $hostsEntry = "127.0.0.1 {$tenant->domain}";
            $hostsContent = File::get('/etc/hosts');
            if (!str_contains($hostsContent, $tenant->domain)) {
                shell_exec("echo '{$hostsEntry}' | sudo tee -a /etc/hosts > /dev/null");
                $this->line("   ✅ Added to /etc/hosts: {$hostsEntry}");
            } else {
                $this->line("   ✓ Already in /etc/hosts");
            }

        } catch (\Exception $e) {
            $this->warn("   ⚠️  Could not write nginx config: " . $e->getMessage());
            $this->line("   Manual nginx config needed for domain: {$tenant->domain}");
        }

        $this->newLine();
    }

    /**
     * Run an artisan command via Symfony Process and stream stdout line-by-line
     * through the given callback. Throws if the command exits non-zero.
     */
    private function streamArtisanCommand(string $artisanArgs, callable $onLine): void
    {
        $php = PHP_BINARY ?: 'php';
        $cmd = $php . ' artisan ' . $artisanArgs;

        $process = Process::fromShellCommandline($cmd, base_path());
        $process->setTimeout(0); // no timeout for long migrations/seeds

        $buffer = '';
        $process->run(function ($type, $chunk) use (&$buffer, $onLine) {
            $buffer .= $chunk;
            while (($pos = strpos($buffer, "\n")) !== false) {
                $line = substr($buffer, 0, $pos);
                $buffer = substr($buffer, $pos + 1);
                // Strip ANSI escape codes
                $clean = preg_replace('/\x1b\[[0-9;]*[a-zA-Z]/', '', $line);
                $onLine(trim($clean));
            }
        });

        // Flush any remaining buffered content
        if ($buffer !== '') {
            $clean = preg_replace('/\x1b\[[0-9;]*[a-zA-Z]/', '', $buffer);
            $onLine(trim($clean));
        }

        if (!$process->isSuccessful()) {
            throw new \RuntimeException(
                'Command failed (' . $process->getExitCode() . '): ' . trim($process->getErrorOutput() ?: $process->getOutput())
            );
        }
    }
}
