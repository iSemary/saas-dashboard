<?php

namespace App\Console\Commands\ProjectSetup;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class SeedDummyDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:dummy-data 
                            {--force : Force seeding even if data exists}
                            {--modules=* : Specific modules to seed (e.g., --modules=Auth,Utilities)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed dummy data for testing and development purposes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🌱 Starting dummy data seeding...');
        $this->newLine();

        $force = $this->option('force');
        $modules = $this->option('modules');

        try {
            // Check if we should proceed
            if (!$force && $this->hasExistingData()) {
                if (!$this->confirm('Existing data found. Do you want to continue? This may create duplicates.')) {
                    $this->info('Seeding cancelled.');
                    return 0;
                }
            }

            // Seed dummy data for each module
            $this->seedModuleData($modules);

            $this->newLine();
            $this->info('✅ Dummy data seeding completed successfully!');
            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Error during seeding: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            return 1;
        }
    }

    /**
     * Check if there's existing data in the database
     */
    private function hasExistingData(): bool
    {
        try {
            // Check for existing users
            $userCount = DB::connection('landlord')->table('users')->count();
            if ($userCount > 0) {
                return true;
            }

            // Check for existing categories
            $categoryCount = DB::connection('landlord')->table('categories')->count();
            if ($categoryCount > 0) {
                return true;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Seed dummy data for specified modules
     */
    private function seedModuleData(array $modules = []): void
    {
        $availableModules = [
            'Auth' => [
                'description' => 'Authentication and user management',
                'seeders' => [
                    'Modules\\Auth\\Database\\Seeders\\DummyUserSeeder',
                ]
            ],
            'Utilities' => [
                'description' => 'Categories, types, industries, and other utilities',
                'seeders' => [
                    'Modules\\Utilities\\Database\\Seeders\\DummyCategorySeeder',
                    'Modules\\Utilities\\Database\\Seeders\\DummyTypeSeeder',
                    'Modules\\Utilities\\Database\\Seeders\\DummyIndustrySeeder',
                    'Modules\\Utilities\\Database\\Seeders\\DummyTagSeeder',
                ]
            ],
                'Email' => [
                    'description' => 'Email templates, campaigns, and related entities',
                    'seeders' => [
                        'Modules\\Email\\Database\\Seeders\\DummyEmailCampaignSeeder',
                        'Modules\\Email\\Database\\Seeders\\EmailGroupSeeder',
                        'Modules\\Email\\Database\\Seeders\\EmailRecipientSeeder',
                        'Modules\\Email\\Database\\Seeders\\EmailSubscriberSeeder',
                        'Modules\\Email\\Database\\Seeders\\EmailTemplateLogSeeder',
                        'Modules\\Email\\Database\\Seeders\\EmailRecipientGroupSeeder',
                        'Modules\\Email\\Database\\Seeders\\EmailRecipientMetaSeeder',
                        'Modules\\Email\\Database\\Seeders\\EmailLogSeeder',
                        'Modules\\Email\\Database\\Seeders\\EmailAttachmentSeeder',
                    ]
                ],
                'Geography' => [
                    'description' => 'Countries, provinces, cities, towns, and streets',
                    'seeders' => [
                        'Modules\\Geography\\Database\\Seeders\\DummyGeographySeeder',
                        'Modules\\Geography\\Database\\Seeders\\TownSeeder',
                        'Modules\\Geography\\Database\\Seeders\\StreetSeeder',
                    ]
                ],
            'Tenant' => [
                'description' => 'Tenant and customer data',
                'seeders' => [
                    'Modules\\Tenant\\Database\\Seeders\\DummyTenantSeeder',
                ]
            ]
        ];

        // If no specific modules provided, seed all
        if (empty($modules)) {
            $modules = array_keys($availableModules);
        }

        foreach ($modules as $module) {
            if (!isset($availableModules[$module])) {
                $this->warn("⚠️  Module '{$module}' not found. Skipping...");
                continue;
            }

            $this->info("📦 Seeding {$module} module: {$availableModules[$module]['description']}");
            
            foreach ($availableModules[$module]['seeders'] as $seeder) {
                try {
                    $this->line("   → Running {$seeder}...");
                    Artisan::call('db:seed', [
                        '--class' => $seeder,
                        '--database' => 'landlord'
                    ]);
                    $this->line("   ✅ {$seeder} completed");
                } catch (\Exception $e) {
                    $this->error("   ❌ {$seeder} failed: " . $e->getMessage());
                }
            }
            
            $this->newLine();
        }
    }
}
