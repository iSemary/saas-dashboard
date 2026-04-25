<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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
            // Unified lowercase paths: database/migrations (lowercase d, lowercase m)
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

        $isFresh = $this->option('fresh');

        // Run migrations using tenant context with all paths combined
        $tenant->execute(function () use ($isFresh, $migrationPaths) {
            if (!$isFresh) {
                $this->markExistingCreateTableMigrationsAsRan($migrationPaths);
            }

            $params = [
                '--database' => 'tenant',
                '--force' => true,
                '--path' => $migrationPaths,
            ];

            if ($isFresh) {
                // migrate:fresh drops all tables then runs only the specified paths
                Artisan::call('migrate:fresh', $params);
            } else {
                Artisan::call('migrate', $params);
            }
        });

        $output = Artisan::output();
        if (trim($output) && !str_contains($output, 'Nothing to migrate')) {
            $this->line("   ✅ All tenant migrations completed successfully");
        } else {
            $this->line("   ⏭️  No migrations found");
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
     * Run tenant seeding
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
            // Unified lowercase paths: database/seeders (lowercase d, lowercase s)
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

        foreach ($seederPaths as $path) {
            try {
                $this->line("   Seeding from: {$path}");
                $this->runTenantSeedersFromPath($path, $tenant);
            } catch (\Exception $e) {
                $this->warn("   ⚠️  Warning: {$path} - {$e->getMessage()}");
            }
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
     * Run all tenant seeders from a specific path
     */
    private function runTenantSeedersFromPath($path, $tenant)
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
                    $this->line("      Seeding: {$fileName}");

                    $tenant->execute(function () use ($className) {
                        Artisan::call('db:seed', [
                            '--class' => $className,
                            '--database' => 'tenant',
                            '--force' => true,
                        ]);
                    });

                    $this->line("      ✅ {$fileName} seeded successfully");
                } catch (\Exception $e) {
                    $this->warn("      ⚠️  Warning: {$fileName} - {$e->getMessage()}");
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
}
