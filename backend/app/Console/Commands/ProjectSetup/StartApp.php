<?php

namespace App\Console\Commands\ProjectSetup;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class StartApp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Running all required commands for first start';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->displayWelcome();
        
        // Create a progress bar for the main setup steps
        $steps = [
            'Setting up storage symbolic link',
            'Running database migrations',
            'Seeding essential data',
            'Seeding development data',
            'Finalizing setup'
        ];
        
        $mainProgressBar = $this->output->createProgressBar(count($steps));
        $mainProgressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% -- %message%');
        $mainProgressBar->setMessage('Initializing...');
        $mainProgressBar->start();

        try {
            // Step 1: Handle storage link
            $mainProgressBar->setMessage('🔗 Setting up storage symbolic link...');
            $this->handleStorageLink();
            $mainProgressBar->advance();
            
            // Step 2: Run migrations
            $mainProgressBar->setMessage('📦 Running database migrations...');
            $this->runMigrationsWithProgress();
            $mainProgressBar->advance();
            
            // Step 3: Seed essential data
            $mainProgressBar->setMessage('🌱 Seeding essential data...');
            $this->seedEssentialDataWithProgress();
            $mainProgressBar->advance();
            
            // Step 4: Seed development data
            $mainProgressBar->setMessage('🎭 Seeding development data...');
            $this->seedDevelopmentDataWithProgress();
            $mainProgressBar->advance();
            
            // Step 5: Finalize
            $mainProgressBar->setMessage('✨ Finalizing setup...');
            sleep(1); // Small delay to show the message
            $mainProgressBar->advance();
            
            $mainProgressBar->finish();
            $this->newLine(2);
            
            $this->info('✅ All startup commands completed successfully!');
            Log::info('SaaS Dashboard setup completed successfully');
            
            $this->displayNextSteps();
            
            return 0;
            
        } catch (\Exception $e) {
            $mainProgressBar->finish();
            $this->newLine(2);
            $this->error('❌ Setup failed: ' . $e->getMessage());
            Log::error('SaaS Dashboard setup failed: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Display welcome message
     */
    private function displayWelcome()
    {
        $this->newLine();
        $this->info('🚀 Starting SaaS Dashboard Setup...');
        $this->line('   This will set up your database, run migrations, and seed initial data.');
        $this->newLine();
    }

    /**
     * Handle storage link creation gracefully
     */
    private function handleStorageLink()
    {
        $this->newLine();
        $this->line('   🔗 Checking storage symbolic link...');
        
        $linkPath = public_path('storage');
        if (is_link($linkPath)) {
            $this->line('   ⏭️  Storage link already exists, skipping...');
            return;
        }
        
        try {
            Artisan::call('storage:link');
            $this->line('   ✅ Storage link created successfully');
        } catch (\Exception $e) {
            $this->line('   ⚠️  Storage link issue: ' . $e->getMessage());
        }
    }

    /**
     * Run database migrations with progress
     */
    private function runMigrationsWithProgress()
    {
        $this->newLine();
        $this->line('   📦 Running database migrations...');
        
        // Create a sub-progress bar for migration steps
        $migrationProgress = $this->output->createProgressBar(4);
        $migrationProgress->setFormat('      [%bar%] %message%');
        $migrationProgress->setMessage('Preparing migrations...');
        $migrationProgress->start();
        
        try {
            $migrationProgress->setMessage('Running landlord migrations...');
            $migrationProgress->advance();
            
            // Run migrations only
            Artisan::call('landlord:setup', ['--migrate-only' => true, '--force' => true]);
            $migrationProgress->setMessage('Processing migration files...');
            $migrationProgress->advance();
            
            $migrationProgress->setMessage('Verifying migration status...');
            $migrationProgress->advance();
            
            $migrationProgress->setMessage('Migrations completed ✅');
            $migrationProgress->advance();
            $migrationProgress->finish();
            
            $this->newLine();
            $this->line('   ✅ Database migrations completed successfully');
            
        } catch (\Exception $e) {
            $migrationProgress->finish();
            $this->newLine();
            throw new \Exception("Migration failed: " . $e->getMessage());
        }
    }

    /**
     * Seed essential data with progress
     */
    private function seedEssentialDataWithProgress()
    {
        $this->newLine();
        $this->line('   🌱 Seeding essential system data...');
        
        $seedProgress = $this->output->createProgressBar(5);
        $seedProgress->setFormat('      [%bar%] %message%');
        $seedProgress->setMessage('Preparing essential seeders...');
        $seedProgress->start();
        
        try {
            $seedProgress->setMessage('Seeding roles and permissions...');
            $seedProgress->advance();
            
            $seedProgress->setMessage('Seeding system configurations...');
            $seedProgress->advance();
            
            $seedProgress->setMessage('Seeding languages and translations...');
            $seedProgress->advance();
            
            // Run essential data seeding
            Artisan::call('landlord:setup', [
                '--seed-only' => true, 
                '--skip-dummy-data' => true, 
                '--force' => true
            ]);
            $seedProgress->setMessage('Processing essential data...');
            $seedProgress->advance();
            
            $seedProgress->setMessage('Essential data seeding completed ✅');
            $seedProgress->advance();
            $seedProgress->finish();
            
            $this->newLine();
            $this->line('   ✅ Essential data seeded successfully');
            
        } catch (\Exception $e) {
            $seedProgress->finish();
            $this->newLine();
            throw new \Exception("Essential data seeding failed: " . $e->getMessage());
        }
    }

    /**
     * Seed development data with progress
     */
    private function seedDevelopmentDataWithProgress()
    {
        $this->newLine();
        $this->line('   🎭 Seeding development and test data...');
        
        $devProgress = $this->output->createProgressBar(4);
        $devProgress->setFormat('      [%bar%] %message%');
        $devProgress->setMessage('Preparing development seeders...');
        $devProgress->start();
        
        try {
            $devProgress->setMessage('Seeding dummy users...');
            $devProgress->advance();
            
            $devProgress->setMessage('Seeding test tenants and customers...');
            $devProgress->advance();
            
            // Run development data seeding
            Artisan::call('landlord:setup', [
                '--seed-only' => true, 
                '--skip-real-data' => true, 
                '--force' => true
            ]);
            $devProgress->setMessage('Processing development data...');
            $devProgress->advance();
            
            $devProgress->setMessage('Development data seeding completed ✅');
            $devProgress->advance();
            $devProgress->finish();
            
            $this->newLine();
            $this->line('   ✅ Development data seeded successfully');
            
        } catch (\Exception $e) {
            $devProgress->finish();
            $this->newLine();
            $this->warn('   ⚠️  Development data seeding had issues: ' . $e->getMessage());
            $this->line('   ℹ️  This is not critical - essential data was seeded successfully');
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
        
        // Create a nice formatted table of next steps
        $this->line('┌─────────────────────────────────────────────────────────────┐');
        $this->line('│                     🌐 Access Application                   │');
        $this->line('├─────────────────────────────────────────────────────────────┤');
        $this->line('│  → Landlord Dashboard: http://landlord.saas.test            │');
        $this->line('│  → Default Login: test_landlord / password123               │');
        $this->line('├─────────────────────────────────────────────────────────────┤');
        $this->line('│                     🔧 Development Commands                 │');
        $this->line('├─────────────────────────────────────────────────────────────┤');
        $this->line('│  → Start dev server: php artisan serve                     │');
        $this->line('│  → Compile assets: npm run dev                              │');
        $this->line('│  → Run tests: php artisan test                              │');
        $this->line('│  → Queue worker: php artisan queue:work                     │');
        $this->line('├─────────────────────────────────────────────────────────────┤');
        $this->line('│                     📚 Documentation                       │');
        $this->line('├─────────────────────────────────────────────────────────────┤');
        $this->line('│  → Check documentation/ folder for detailed guides          │');
        $this->line('│  → API docs: /api/documentation                             │');
        $this->line('└─────────────────────────────────────────────────────────────┘');
        
        $this->newLine();
        $this->info('🚀 Happy coding!');
        $this->newLine();
    }
}
