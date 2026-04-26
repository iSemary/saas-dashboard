<?php

namespace App\Console\Commands\ProjectSetup;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class StartApp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:start
                            {--refresh : Drop all landlord and tenant tables and start fresh}';

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

        $refresh = $this->option('refresh');

        // Confirm if refresh is requested (destructive operation)
        if ($refresh) {
            $this->warn('⚠️  WARNING: --refresh will DROP ALL TABLES in both landlord and tenant databases!');
            if (!$this->confirm('Are you sure you want to continue?', false)) {
                $this->info('Operation cancelled.');
                return 0;
            }
        }

        // Create a progress bar for the main setup steps
        $steps = [
            'Ensuring .env file exists',
            'Setting up storage symbolic link',
            'Running database migrations',
            'Seeding essential data',
            'Seeding real data',
            'Running tenant setup',
            'Installing Passport keys',
            'Finalizing setup'
        ];

        $mainProgressBar = $this->output->createProgressBar(count($steps));
        $mainProgressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% -- %message%');
        $mainProgressBar->setMessage('Initializing...');
        $mainProgressBar->start();

        try {
            // Step 1: Ensure .env file
            $mainProgressBar->setMessage('📄 Ensuring .env file exists...');
            $this->ensureEnvFile();
            $mainProgressBar->advance();

            // Step 2: Handle storage link
            $mainProgressBar->setMessage('🔗 Setting up storage symbolic link...');
            $this->handleStorageLink();
            $mainProgressBar->advance();

            // Step 3: Run migrations
            $mainProgressBar->setMessage('📦 Running database migrations...');
            $this->runMigrationsWithProgress($refresh);
            $mainProgressBar->advance();

            // Step 4: Seed essential data
            $mainProgressBar->setMessage('🌱 Seeding essential data...');
            $this->seedEssentialDataWithProgress();
            $mainProgressBar->advance();

            // Step 5: Seed real data
            $mainProgressBar->setMessage('🌍 Seeding real data...');
            $this->seedRealDataWithProgress();
            $mainProgressBar->advance();

            // Step 6: Tenant setup
            $mainProgressBar->setMessage('🏢 Running tenant setup...');
            $this->runTenantSetup($refresh);
            $mainProgressBar->advance();

            // Step 7: Passport keys
            $mainProgressBar->setMessage('🔑 Installing Passport keys...');
            $this->installPassportKeys();
            $mainProgressBar->advance();

            // Step 8: Finalize
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
     * Ensure .env file exists, copy from .env.example if missing and generate app key
     */
    private function ensureEnvFile()
    {
        $this->newLine();
        $this->line('   📄 Checking .env file...');

        $envPath = base_path('.env');
        $examplePath = base_path('.env.example');

        if (file_exists($envPath)) {
            $this->line('   ⏭️  .env file already exists, skipping...');
            return;
        }

        if (!file_exists($examplePath)) {
            $this->line('   ⚠️  No .env.example found, skipping...');
            return;
        }

        try {
            copy($examplePath, $envPath);
            $this->line('   ✅ Copied .env.example → .env');
            Artisan::call('key:generate', ['--force' => true]);
            $this->line('   ✅ Application key generated');
        } catch (\Exception $e) {
            $this->line('   ⚠️  .env setup issue: ' . $e->getMessage());
        }
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
     * Run database migrations with real per-migration progress
     */
    private function runMigrationsWithProgress(bool $refresh = false)
    {
        $this->newLine();
        $this->line('   📦 Running database migrations...');

        // If refresh, drop landlord schema first via fresh on the root path
        if ($refresh) {
            $this->line('      ↻ Dropping landlord tables (migrate:fresh)...');
            Artisan::call('migrate:fresh', [
                '--database' => 'landlord',
                '--path' => 'database/migrations',
                '--force' => true,
            ]);
        }

        // Count all landlord migration files across the standard paths used by landlord:migrate
        $totalMigrations = $this->countLandlordMigrationFiles();

        if ($totalMigrations === 0) {
            $this->line('   ⏭️  No migrations to run');
            return;
        }

        $bar = $this->output->createProgressBar($totalMigrations);
        $bar->setFormat('      %current%/%max% [%bar%] %percent:3s%% -- %message%');
        $bar->setMessage('Starting migrations...');
        $bar->start();

        try {
            $this->streamArtisanCommand(
                'landlord:migrate --no-interaction',
                function (string $line) use ($bar) {
                    // Laravel 10/11 Task component format: "<name> ........... 12.34ms DONE/FAIL"
                    // We only count actual migration files (timestamp-prefixed names).
                    if (preg_match('/^(\d{4}_\d{2}_\d{2}_\d+_\S+)\s+\.{3,}.*\s+(DONE|FAIL)\s*$/', $line, $m)) {
                        $bar->setMessage(($m[2] === 'DONE' ? '✓ ' : '✗ ') . $m[1]);
                        $bar->advance();
                        return;
                    }
                    // Legacy Laravel format fallback
                    if (preg_match('/Migrating:\s+(.+)/', $line, $m)) {
                        $bar->setMessage('Migrating: ' . trim($m[1]));
                    } elseif (preg_match('/Migrated:\s+(.+?)(\s+\(.+\))?\s*$/', $line, $m)) {
                        $bar->setMessage('✓ ' . trim($m[1]));
                        $bar->advance();
                    }
                }
            );

            // In case file count was over-estimated (e.g. nothing pending), finish the bar
            if ($bar->getProgress() < $bar->getMaxSteps()) {
                $bar->finish();
            }
            $bar->setMessage('Migrations completed ✅');
            $bar->finish();

            $this->newLine();
            $this->line('   ✅ Database migrations completed successfully');
        } catch (\Exception $e) {
            $bar->finish();
            $this->newLine();
            throw new \Exception("Migration failed: " . $e->getMessage());
        }
    }

    /**
     * Seed essential data (glob-based base landlord seeders) with real per-seeder progress
     */
    private function seedEssentialDataWithProgress()
    {
        $this->newLine();
        $this->line('   🌱 Seeding essential system data...');

        $totalSeeders = $this->countLandlordBaseSeeders();

        if ($totalSeeders === 0) {
            $this->line('   ⏭️  No essential seeders to run');
            return;
        }

        $bar = $this->output->createProgressBar($totalSeeders);
        $bar->setFormat('      %current%/%max% [%bar%] %percent:3s%% -- %message%');
        $bar->setMessage('Starting essential seeders...');
        $bar->start();

        try {
            $this->streamArtisanCommand(
                'landlord:seed --no-interaction',
                function (string $line) use ($bar) {
                    if (preg_match('/Seeding:\s+(.+)/', $line, $m)) {
                        $bar->setMessage(trim($m[1]));
                    } elseif (preg_match('/✅\s+(\S+)\s+seeded successfully/u', $line, $m)) {
                        $bar->setMessage('✓ ' . trim($m[1]));
                        $bar->advance();
                    }
                }
            );

            $bar->setMessage('Essential data completed ✅');
            $bar->finish();

            $this->newLine();
            $this->line('   ✅ Essential data seeded successfully');
        } catch (\Exception $e) {
            $bar->finish();
            $this->newLine();
            $this->warn('   ⚠️  Essential data seeding had issues: ' . $e->getMessage());
        }
    }

    /**
     * Seed real data with real per-seeder progress
     */
    private function seedRealDataWithProgress()
    {
        $this->newLine();
        $this->line('   🌍 Seeding real data...');

        $totalSeeders = $this->countRealDataSeeders();

        if ($totalSeeders === 0) {
            $this->line('   ⏭️  No real data seeders to run');
            return;
        }

        $bar = $this->output->createProgressBar($totalSeeders);
        $bar->setFormat('      %current%/%max% [%bar%] %percent:3s%% -- %message%');
        $bar->setMessage('Starting real data seeders...');
        $bar->start();

        try {
            $this->streamArtisanCommand(
                'seed:real-data --force --no-interaction',
                function (string $line) use ($bar) {
                    // Module header: "📦 Seeding {Module} module: ..."
                    if (preg_match('/📦\s+Seeding\s+(\S+)\s+module/u', $line, $m)) {
                        $bar->setMessage('Module: ' . $m[1]);
                        return;
                    }
                    // Per-seeder start: "   → Running {Class}..."
                    if (preg_match('/→\s+Running\s+(\S+?)\.\.\./u', $line, $m)) {
                        $shortName = $this->shortClassName($m[1]);
                        $bar->setMessage($shortName);
                        return;
                    }
                    // Per-seeder done: "   ✅ {Class} completed"
                    if (preg_match('/✅\s+(\S+)\s+completed/u', $line, $m)) {
                        $shortName = $this->shortClassName($m[1]);
                        $bar->setMessage('✓ ' . $shortName);
                        $bar->advance();
                    }
                }
            );

            $bar->setMessage('Real data completed ✅');
            $bar->finish();

            $this->newLine();
            $this->line('   ✅ Real data seeded successfully');
        } catch (\Exception $e) {
            $bar->finish();
            $this->newLine();
            $this->warn('   ⚠️  Real data seeding had issues: ' . $e->getMessage());
        }
    }

    // ─────────────────────────────────────────────────────────────────────
    // Helpers: counting and streaming
    // ─────────────────────────────────────────────────────────────────────

    /**
     * Count all landlord migration files across the paths used by landlord:migrate
     */
    private function countLandlordMigrationFiles(): int
    {
        $paths = [
            base_path('database/migrations'),
            base_path('database/migrations/shared'),
            base_path('database/migrations/landlord'),
        ];

        $globPatterns = [
            base_path('modules/*/Database/Migrations/landlord'),
            base_path('modules/*/Database/Migrations/shared'),
            base_path('modules/*/Database/migrations/landlord'),
            base_path('modules/*/Database/migrations/shared'),
            base_path('modules/*/database/migrations/landlord'),
            base_path('modules/*/database/migrations/shared'),
        ];

        foreach ($globPatterns as $pattern) {
            $matches = glob($pattern, GLOB_ONLYDIR) ?: [];
            $paths = array_merge($paths, $matches);
        }

        $count = 0;
        $seen = [];
        foreach ($paths as $path) {
            if (!is_dir($path)) {
                continue;
            }
            $files = glob(rtrim($path, '/') . '/*.php') ?: [];
            foreach ($files as $file) {
                // Migration files in the root database/migrations directory only count once
                $real = realpath($file);
                if ($real && !isset($seen[$real])) {
                    $seen[$real] = true;
                    $count++;
                }
            }
        }

        return $count;
    }

    /**
     * Count landlord base seeder files (those discovered by landlord:seed)
     */
    private function countLandlordBaseSeeders(): int
    {
        $paths = [
            base_path('database/seeders/Landlord'),
            base_path('database/seeders/landlord'),
        ];

        $globPatterns = [
            base_path('modules/*/Database/Seeders/Landlord'),
            base_path('modules/*/Database/Seeders/landlord'),
            base_path('modules/*/Database/Seeders/shared'),
            base_path('modules/*/Database/seeders/Landlord'),
            base_path('modules/*/Database/seeders/landlord'),
            base_path('modules/*/Database/seeders/shared'),
            base_path('modules/*/database/Seeders/Landlord'),
            base_path('modules/*/database/Seeders/landlord'),
            base_path('modules/*/database/Seeders/shared'),
            base_path('modules/*/database/seeders/Landlord'),
            base_path('modules/*/database/seeders/landlord'),
            base_path('modules/*/database/seeders/shared'),
        ];

        foreach ($globPatterns as $pattern) {
            $matches = glob($pattern, GLOB_ONLYDIR) ?: [];
            $paths = array_merge($paths, $matches);
        }

        $count = 0;
        $seen = [];
        foreach ($paths as $path) {
            if (!is_dir($path)) {
                continue;
            }
            $files = glob(rtrim($path, '/') . '/*Seeder.php') ?: [];
            foreach ($files as $file) {
                $real = realpath($file);
                if ($real && !isset($seen[$real])) {
                    $seen[$real] = true;
                    $count++;
                }
            }
        }

        return $count;
    }

    /**
     * Count real-data seeders by inspecting SeedRealDataCommand's module list.
     * Falls back to a static estimate if reflection fails.
     */
    private function countRealDataSeeders(): int
    {
        try {
            $ref = new \ReflectionClass(SeedRealDataCommand::class);
            $method = $ref->getMethod('seedModuleData');
            $method->setAccessible(true);

            // Read the source of the method and naively count seeder string entries.
            // SeedRealDataCommand defines them inline; matching 'Modules\\...Seeder' or 'Database\\Seeders\\...Seeder'
            $source = file_get_contents($ref->getFileName());
            preg_match_all('/[\'"]([A-Za-z_][A-Za-z0-9_\\\\]*Seeder)[\'"]/', $source, $matches);
            $unique = array_unique($matches[1] ?? []);
            $count = count($unique);
            return $count > 0 ? $count : 25;
        } catch (\Throwable $e) {
            return 25;
        }
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

    /**
     * Get a short class name from a fully-qualified namespace path
     */
    private function shortClassName(string $fqcn): string
    {
        $parts = explode('\\', $fqcn);
        return end($parts) ?: $fqcn;
    }

    /**
     * Run tenant setup (migrations + seed)
     */
    private function runTenantSetup(bool $refresh = false)
    {
        $this->newLine();
        $this->line('   🏢 Running tenant setup...');

        try {
            $params = ['--force' => true];
            if ($refresh) {
                $params['--fresh'] = true;
            }
            $exitCode = Artisan::call('tenant:setup', $params);
            $output = Artisan::output();
            if ($output) {
                foreach (explode("\n", trim($output)) as $line) {
                    if ($line) {
                        $this->line('      ' . $line);
                    }
                }
            }
            if ($exitCode === 0) {
                $this->line('   ✅ Tenant setup completed successfully');
            } else {
                $this->warn('   ⚠️  Tenant setup exited with code: ' . $exitCode);
            }
        } catch (\Exception $e) {
            $this->warn('   ⚠️  Tenant setup had issues: ' . $e->getMessage());
            $this->line('   ℹ️  This is not critical - you can run tenant:setup manually later');
        }
    }

    /**
     * Install Passport keys if they don't exist
     */
    private function installPassportKeys()
    {
        $this->newLine();
        $this->line('   🔑 Checking Passport keys...');

        $privateKeyPath = storage_path('oauth-private.key');

        if (file_exists($privateKeyPath)) {
            $this->line('   ⏭️  Passport keys already exist, skipping...');
            return;
        }

        try {
            Artisan::call('passport:keys', ['--force' => true]);
            $this->line('   ✅ Passport keys installed successfully');
        } catch (\Exception $e) {
            $this->warn('   ⚠️  Passport keys issue: ' . $e->getMessage());
            $this->line('   ℹ️  You can run passport:keys manually later');
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
