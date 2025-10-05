<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Modules\Tenant\Entities\Tenant;

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
                            {--fresh : Run fresh migrations (drop all tables and re-run migrations)}';

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
        $this->info('🏗️  Starting Tenant Database Setup...');
        $this->newLine();

        $tenantInput = $this->argument('tenant');
        $tenants = $this->getTenants($tenantInput);

        if ($tenants->isEmpty()) {
            $this->error('No tenants found (excluding landlord). Please create a tenant first.');
            return 1;
        }

        foreach ($tenants as $tenant) {
            $this->info("📦 Setting up tenant: {$tenant->name} (ID: {$tenant->id})");
            $this->line("   Database: {$tenant->database}");
            $this->line("   Domain: {$tenant->domain}");
            $this->newLine();

            // Check if we should run migrations
            if (!$this->option('seed-only')) {
                $this->runTenantMigrations($tenant);
            }

            // Check if we should run seeding
            if (!$this->option('migrate-only')) {
                $this->runTenantSeeding($tenant);
            }

            // Setup Passport for tenant
            $this->setupPassportForTenant($tenant);

            $this->newLine();
            $this->info("✅ Tenant '{$tenant->name}' setup completed successfully!");
            $this->newLine();
        }

        $this->info('🎉 All tenant setups completed successfully!');
        return 0;
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
     * Run all tenant migrations
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
            $modules = glob($moduleBasePath . '/*/Database/migrations/tenant', GLOB_ONLYDIR);
            foreach ($modules as $modulePath) {
                $relativePath = str_replace(base_path() . '/', '', $modulePath);
                $migrationPaths[] = $relativePath;
            }
            
            $modules = glob($moduleBasePath . '/*/Database/Migrations/tenant', GLOB_ONLYDIR);
            foreach ($modules as $modulePath) {
                $relativePath = str_replace(base_path() . '/', '', $modulePath);
                $migrationPaths[] = $relativePath;
            }
            
            $modules = glob($moduleBasePath . '/*/Database/migrations/shared', GLOB_ONLYDIR);
            foreach ($modules as $modulePath) {
                $relativePath = str_replace(base_path() . '/', '', $modulePath);
                $migrationPaths[] = $relativePath;
            }
            
            $modules = glob($moduleBasePath . '/*/Database/Migrations/shared', GLOB_ONLYDIR);
            foreach ($modules as $modulePath) {
                $relativePath = str_replace(base_path() . '/', '', $modulePath);
                $migrationPaths[] = $relativePath;
            }
        }

        $migrateCommand = $this->option('fresh') ? 'migrate:fresh' : 'migrate';
        $forceFlag = $this->option('force') ? ' --force' : '';

        foreach ($migrationPaths as $path) {
            try {
                $this->line("   Running migrations from: {$path}");
                
                $command = "tenants:artisan '{$migrateCommand} --path={$path} --database=tenant{$forceFlag}' --tenant={$tenant->id}";
                Artisan::call($command);
                
                $output = Artisan::output();
                if (trim($output) && !str_contains($output, 'Nothing to migrate')) {
                    $this->line("   ✅ Migrations completed for: {$path}");
                } else {
                    $this->line("   ⏭️  No migrations found in: {$path}");
                }
            } catch (\Exception $e) {
                $this->warn("   ⚠️  Warning: {$path} - {$e->getMessage()}");
            }
        }

        $this->newLine();
    }

    /**
     * Run tenant seeding
     */
    private function runTenantSeeding($tenant)
    {
        $this->info('🌱 Running Tenant Seeding...');
        
        try {
            $this->line("   Seeding tenant database...");
            
            $command = "tenants:artisan 'migrate --database=tenant --seed{$this->getForceFlag()}' --tenant={$tenant->id}";
            Artisan::call($command);
            
            $output = Artisan::output();
            if (trim($output)) {
                $this->line("   ✅ Seeding completed for tenant: {$tenant->name}");
            } else {
                $this->line("   ⏭️  No seeders found or already seeded");
            }
        } catch (\Exception $e) {
            $this->warn("   ⚠️  Warning: Seeding failed - {$e->getMessage()}");
        }

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
     * Setup Passport for tenant
     */
    private function setupPassportForTenant($tenant)
    {
        $this->info('🔑 Setting up Passport for tenant...');
        
        try {
            $this->line("   Running Passport setup seeder...");
            
            $command = "tenants:artisan 'db:seed --class=Database\\Seeders\\Tenant\\PassportSetupSeeder --database=tenant{$this->getForceFlag()}' --tenant={$tenant->id}";
            Artisan::call($command);
            
            $output = Artisan::output();
            if (trim($output)) {
                $this->line("   ✅ Passport setup completed for tenant: {$tenant->name}");
            } else {
                $this->line("   ⏭️  Passport setup skipped or already configured");
            }
        } catch (\Exception $e) {
            $this->warn("   ⚠️  Warning: Passport setup failed - {$e->getMessage()}");
            $this->line("   Manual Passport setup may be required for tenant: {$tenant->name}");
        }

        $this->newLine();
    }
}
