<?php

namespace App\Console\Commands\ProjectSetup;

use Illuminate\Console\Command;

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

        try {
            $this->call('landlord:migrate');
            $this->info('✅ Landlord migrations completed successfully');
        } catch (\Exception $e) {
            $this->warn("⚠️  Migration warning: {$e->getMessage()}");
        }

        $this->newLine();
    }

    /**
     * Run all landlord seeding
     */
    private function runSeeding()
    {
        $this->info('🌱 Running Landlord Seeding...');

        // Run landlord:seed for seeder files
        if (!$this->option('skip-real-data') && !$this->option('skip-dummy-data')) {
            try {
                $this->call('landlord:seed');
                $this->info('✅ Landlord seeders completed successfully');
            } catch (\Exception $e) {
                $this->warn("⚠️  Seeding warning: {$e->getMessage()}");
            }
        }

        // Seed real data using seed:real-data command
        if (!$this->option('skip-real-data')) {
            $this->newLine();
            try {
                $this->call('seed:real-data', ['--force' => true]);
                $this->info('✅ Real data seeding completed successfully');
            } catch (\Exception $e) {
                $this->warn("⚠️  Real data seeding warning: {$e->getMessage()}");
            }
        }

        // Seed dummy data using seed:dummy-data command
        if (!$this->option('skip-dummy-data')) {
            $this->newLine();
            try {
                $this->call('seed:dummy-data', ['--force' => true]);
                $this->info('✅ Dummy data seeding completed successfully');
            } catch (\Exception $e) {
                $this->warn("⚠️  Dummy data seeding warning: {$e->getMessage()}");
            }
        }

        $this->newLine();
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
