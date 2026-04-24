<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class LandlordSetupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'landlord:setup 
                            {--force : Force the operation without confirmation}
                            {--migrate-only : Run migrations only, skip seeding}
                            {--seed-only : Run seeding only, skip migrations}
                            {--skip-real-data : Skip seeding real data (languages, email templates, etc.)}
                            {--skip-dummy-data : Skip seeding dummy data for development}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Complete landlord database setup: run migrations and seed all necessary data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🏗️  Starting Landlord Database Setup...');
        $this->newLine();

        // Check if we should run migrations
        if (!$this->option('seed-only')) {
            $this->runMigrations();
        }

        // Check if we should run seeding
        if (!$this->option('migrate-only')) {
            $this->runSeeding();
        }

        $this->newLine();
        $this->info('✅ Landlord database setup completed successfully!');

        // Display next steps
        $this->displayNextSteps();
    }

    /**
     * Run all landlord migrations
     */
    private function runMigrations()
    {
        $this->info('📦 Running Landlord Migrations...');

        $migrationPaths = [
            'database/migrations/landlord',
            'database/migrations/shared',
        ];

        // Dynamically find all module migration paths
        $moduleBasePath = base_path('modules');
        if (is_dir($moduleBasePath)) {
            // Standardized paths: Database/migrations (lowercase m)
            $modules = glob($moduleBasePath . '/*/Database/migrations/landlord', GLOB_ONLYDIR);
            foreach ($modules as $modulePath) {
                $relativePath = str_replace(base_path() . '/', '', $modulePath);
                $migrationPaths[] = $relativePath;
            }

            $modules = glob($moduleBasePath . '/*/Database/migrations/shared', GLOB_ONLYDIR);
            foreach ($modules as $modulePath) {
                $relativePath = str_replace(base_path() . '/', '', $modulePath);
                $migrationPaths[] = $relativePath;
            }
        }

        foreach ($migrationPaths as $path) {
            try {
                $this->line("   Running migrations from: {$path}");
                Artisan::call('migrate', [
                    '--path' => $path,
                    '--database' => 'landlord',
                    '--force' => true
                ]);

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
     * Run all landlord seeding
     */
    private function runSeeding()
    {
        $this->info('🌱 Running Landlord Seeding...');

        // Seed real data first (essential data)
        if (!$this->option('skip-real-data')) {
            $this->seedRealData();
        }

        // Seed dummy data for development
        if (!$this->option('skip-dummy-data')) {
            $this->seedDummyData();
        }

        $this->newLine();
    }

    /**
     * Seed real/essential data
     */
    private function seedRealData()
    {
        $this->line('   📋 Seeding Real Data...');

        $seederPaths = [
            'database/seeders/Landlord',
        ];

        // Dynamically find all module seeder paths
        $moduleBasePath = base_path('modules');
        if (is_dir($moduleBasePath)) {
            // Standardized paths: Database/Seeders (capital S)
            $modules = glob($moduleBasePath . '/*/Database/Seeders/Landlord', GLOB_ONLYDIR);
            foreach ($modules as $modulePath) {
                $relativePath = str_replace(base_path() . '/', '', $modulePath);
                $seederPaths[] = $relativePath;
            }

            $modules = glob($moduleBasePath . '/*/Database/Seeders/Shared', GLOB_ONLYDIR);
            foreach ($modules as $modulePath) {
                $relativePath = str_replace(base_path() . '/', '', $modulePath);
                $seederPaths[] = $relativePath;
            }
        }

        foreach ($seederPaths as $path) {
            try {
                $this->line("      Seeding from: {$path}");
                $this->runSeedersFromPath($path, 'landlord');
            } catch (\Exception $e) {
                $this->warn("      ⚠️  Warning: {$path} - {$e->getMessage()}");
            }
        }
    }

    /**
     * Seed dummy data for development
     */
    private function seedDummyData()
    {
        $this->line('   🎭 Seeding Dummy Data...');

        $seederPaths = [
            'database/seeders/Landlord',
        ];

        // Dynamically find all module seeder paths
        $moduleBasePath = base_path('modules');
        if (is_dir($moduleBasePath)) {
            // Standardized paths: Database/Seeders (capital S)
            $modules = glob($moduleBasePath . '/*/Database/Seeders/Landlord', GLOB_ONLYDIR);
            foreach ($modules as $modulePath) {
                $relativePath = str_replace(base_path() . '/', '', $modulePath);
                $seederPaths[] = $relativePath;
            }

            $modules = glob($moduleBasePath . '/*/Database/Seeders/Shared', GLOB_ONLYDIR);
            foreach ($modules as $modulePath) {
                $relativePath = str_replace(base_path() . '/', '', $modulePath);
                $seederPaths[] = $relativePath;
            }
        }

        foreach ($seederPaths as $path) {
            try {
                $this->line("      Seeding from: {$path}");
                $this->runSeedersFromPath($path, 'landlord', true); // true for dummy data
            } catch (\Exception $e) {
                $this->warn("      ⚠️  Warning: {$path} - {$e->getMessage()}");
            }
        }
    }

    /**
     * Run all seeders from a specific path
     */
    private function runSeedersFromPath($path, $database, $dummyData = false)
    {
        if (!is_dir(base_path($path))) {
            return;
        }

        $seederFiles = glob(base_path($path) . '/*Seeder.php');

        foreach ($seederFiles as $seederFile) {
            $fileName = basename($seederFile, '.php');

            // Skip dummy seeders when seeding real data
            if (!$dummyData && str_contains(strtolower($fileName), 'dummy')) {
                continue;
            }

            // Skip non-dummy seeders when seeding dummy data
            if ($dummyData && !str_contains(strtolower($fileName), 'dummy')) {
                continue;
            }

            // Convert file path to class name
            $className = $this->getSeederClassName($seederFile, $path);

            if ($className) {
                try {
                    $this->line("         Seeding: {$fileName}");
                    Artisan::call('db:seed', [
                        '--class' => $className,
                        '--database' => $database,
                        '--force' => true
                    ]);
                    $this->line("         ✅ {$fileName} seeded successfully");
                } catch (\Exception $e) {
                    $this->warn("         ⚠️  Warning: {$fileName} - {$e->getMessage()}");
                }
            }
        }
    }

    /**
     * Get seeder class name from file path
     */
    private function getSeederClassName($filePath, $basePath)
    {
        $relativePath = str_replace(base_path($basePath) . '/', '', $filePath);
        $relativePath = str_replace('.php', '', $relativePath);

        // Handle different path structures
        if (str_starts_with($basePath, 'database/seeders/')) {
            return 'Database\\Seeders\\' . str_replace('/', '\\', $relativePath);
        }

        if (str_contains($basePath, 'modules/')) {
            // Extract module name from path
            $pathParts = explode('/', $basePath);
            $moduleIndex = array_search('modules', $pathParts);
            if ($moduleIndex !== false && isset($pathParts[$moduleIndex + 1])) {
                $moduleName = $pathParts[$moduleIndex + 1];
                $seederPath = str_replace($moduleName . '/Database/', '', $relativePath);
                return "Modules\\{$moduleName}\\Database\\Seeders\\" . str_replace('/', '\\', $seederPath);
            }
        }

        return null;
    }

    /**
     * Display next steps after setup
     */
    private function displayNextSteps()
    {
        $this->newLine();
        $this->info('🎉 Setup Complete! Next Steps:');
        $this->newLine();

        $this->line('1. 🌐 Access the application:');
        $this->line('   - Landlord Dashboard: http://landlord.saas.test');
        $this->line('   - Default Login: test_landlord / password123');
        $this->newLine();

        $this->line('2. 🔧 Additional setup (if needed):');
        $this->line('   - Generate OAuth2 keys: php artisan passport:keys --force');
        $this->line('   - Create storage symlink: php artisan storage:link');
        $this->line('   - Compile assets: npm run dev');
        $this->newLine();

        $this->line('3. 📚 Documentation:');
        $this->line('   - Environment Setup: documentation/environment-setup.md');
        $this->line('   - Multi-Tenant Architecture: documentation/multi-tenant-architecture.md');
        $this->line('   - Email System: documentation/email-system.md');
        $this->newLine();

        $this->line('4. 🧪 Testing:');
        $this->line('   - Run tests: php artisan test');
        $this->line('   - Test coverage: php artisan test --coverage');
        $this->newLine();

        $this->info('Happy coding! 🚀');
    }
}