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
            'modules/*/Database/migrations/landlord',
            'modules/*/Database/migrations/shared',
            'database/migrations/shared'
        ];

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

        $realDataSeeders = [
            // Core system data
            'Database\\Seeders\\Landlord\\RolePermissionSeeder' => 'Roles and Permissions',
            'Database\\Seeders\\Landlord\\LandlordTenantSeeder' => 'Landlord Tenant',
            'Modules\\Auth\\Database\\Seeders\\LandlordUserSeeder' => 'Default Landlord User',
            'Modules\\Utilities\\Database\\Seeders\\ModulesSeeder' => 'System Modules',
            'Modules\\Development\\Database\\Seeders\\ConfigurationsSeeder' => 'System Configurations',
            
            // Localization
            'Modules\\Localization\\Database\\Seeders\\LanguageSeeder' => 'Languages',
            'Modules\\Localization\\Database\\Seeders\\TranslationSeeder' => 'Translations',
            
            // Email system
            'Modules\\Email\\Database\\Seeders\\EmailTemplateSeeder' => 'Email Templates',
            'Modules\\Email\\Database\\Seeders\\EmailCredentialSeeder' => 'Email Credentials',
        ];

        foreach ($realDataSeeders as $seeder => $description) {
            try {
                $this->line("      Seeding: {$description}");
                Artisan::call('db:seed', [
                    '--class' => $seeder,
                    '--database' => 'landlord',
                    '--force' => true
                ]);
                $this->line("      ✅ {$description} seeded successfully");
            } catch (\Exception $e) {
                $this->warn("      ⚠️  Warning: {$description} - {$e->getMessage()}");
            }
        }
    }

    /**
     * Seed dummy data for development
     */
    private function seedDummyData()
    {
        $this->line('   🎭 Seeding Dummy Data...');

        $dummyDataSeeders = [
            // Auth module
            'Modules\\Auth\\Database\\Seeders\\DummyUserSeeder' => 'Dummy Users',
            
            // Utilities module
            'Modules\\Utilities\\Database\\Seeders\\DummyCategorySeeder' => 'Dummy Categories',
            'Modules\\Utilities\\Database\\Seeders\\DummyTypeSeeder' => 'Dummy Types',
            'Modules\\Utilities\\Database\\Seeders\\DummyIndustrySeeder' => 'Dummy Industries',
            'Modules\\Utilities\\Database\\Seeders\\DummyTagSeeder' => 'Dummy Tags',
            
            // Email module
            'Modules\\Email\\Database\\Seeders\\DummyEmailCampaignSeeder' => 'Dummy Email Campaigns',
            
            // Geography module
            'Modules\\Geography\\Database\\Seeders\\DummyGeographySeeder' => 'Dummy Geography Data',
            
            // Tenant module
            'Modules\\Tenant\\Database\\Seeders\\DummyTenantSeeder' => 'Dummy Tenants',
        ];

        foreach ($dummyDataSeeders as $seeder => $description) {
            try {
                $this->line("      Seeding: {$description}");
                Artisan::call('db:seed', [
                    '--class' => $seeder,
                    '--database' => 'landlord',
                    '--force' => true
                ]);
                $this->line("      ✅ {$description} seeded successfully");
            } catch (\Exception $e) {
                $this->warn("      ⚠️  Warning: {$description} - {$e->getMessage()}");
            }
        }
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
