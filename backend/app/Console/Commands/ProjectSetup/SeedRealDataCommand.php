<?php

namespace App\Console\Commands\ProjectSetup;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class SeedRealDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:real-data
                            {--force : Force seeding even if data exists}
                            {--modules=* : Specific modules to seed (e.g., --modules=Localization,Email)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed real data like languages, email templates, configurations, etc.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🌍 Starting real data seeding...');
        $this->newLine();

        $force = $this->option('force');
        $modules = $this->option('modules');

        try {
            // Check if we should proceed
            if (!$force && $this->hasExistingRealData()) {
                if (!$this->confirm('Existing real data found. Do you want to continue? This may create duplicates.')) {
                    $this->info('Seeding cancelled.');
                    return 0;
                }
            }

            // Seed real data for each module
            $this->seedModuleData($modules);

            $this->newLine();
            $this->info('✅ Real data seeding completed successfully!');
            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Error during seeding: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            return 1;
        }
    }

    /**
     * Check if there's existing real data in the database
     */
    private function hasExistingRealData(): bool
    {
        try {
            // Check for existing languages
            $languageCount = DB::connection('landlord')->table('languages')->count();
            if ($languageCount > 0) {
                return true;
            }

            // Check for existing email templates
            $emailTemplateCount = DB::connection('landlord')->table('email_templates')->count();
            if ($emailTemplateCount > 0) {
                return true;
            }

            // Check for existing configurations
            $configCount = DB::connection('landlord')->table('configurations')->count();
            if ($configCount > 0) {
                return true;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Seed real data for specified modules
     */
    private function seedModuleData(array $modules = []): void
    {
        $availableModules = [
            'Localization' => [
                'description' => 'Languages and translations',
                'seeders' => [
                    'Modules\\Localization\\Database\\Seeders\\LanguageSeeder',
                    'Modules\\Localization\\Database\\Seeders\\TranslationSeeder',
                ]
            ],
                'Email' => [
                    'description' => 'Email templates, credentials, and related entities',
                    'seeders' => [
                        'Modules\\Email\\Database\\Seeders\\EmailTemplateSeeder',
                        'Modules\\Email\\Database\\Seeders\\EmailCredentialSeeder',
                        'Modules\\Email\\Database\\Seeders\\EmailGroupSeeder',
                        'Modules\\Email\\Database\\Seeders\\EmailRecipientSeeder',
                        'Modules\\Email\\Database\\Seeders\\EmailSubscriberSeeder',
                        'Modules\\Email\\Database\\Seeders\\EmailTemplateLogSeeder',
                        'Modules\\Email\\Database\\Seeders\\EmailRecipientGroupSeeder',
                        'Modules\\Email\\Database\\Seeders\\EmailRecipientMetaSeeder',
                        'Modules\\Email\\Database\\Seeders\\EmailCampaignSeeder',
                        'Modules\\Email\\Database\\Seeders\\EmailLogSeeder',
                        'Modules\\Email\\Database\\Seeders\\EmailAttachmentSeeder',
                    ]
                ],
            'Development' => [
                'description' => 'System configurations and settings',
                'seeders' => [
                    'Modules\\Development\\Database\\Seeders\\ConfigurationsSeeder',
                ]
            ],
                'Utilities' => [
                    'description' => 'System modules, currencies, and utilities',
                    'seeders' => [
                        'Modules\\Utilities\\Database\\Seeders\\ModulesSeeder',
                        'Modules\\Utilities\\Database\\Seeders\\CurrencySeeder',
                        'Modules\\Utilities\\Database\\Seeders\\StaticPageSeeder',
                        'Modules\\Utilities\\Database\\Seeders\\StaticPageAttributeSeeder',
                        'Modules\\Utilities\\Database\\Seeders\\TagSeeder',
                        'Modules\\Utilities\\Database\\Seeders\\IndustrySeeder',
                        'Modules\\Utilities\\Database\\Seeders\\UnitSeeder',
                        'Modules\\Utilities\\Database\\Seeders\\ModuleEntitySeeder',
                        'Modules\\Utilities\\Database\\Seeders\\CategorySeeder',
                    ]
                ],
            'Auth' => [
                'description' => 'Roles, permissions, and default users',
                'seeders' => [
                    'Database\\Seeders\\Landlord\\RolePermissionSeeder',
                    'Modules\\Auth\\Database\\Seeders\\LandlordUserSeeder',
                ]
            ],
                'Tenant' => [
                    'description' => 'Landlord tenant setup',
                    'seeders' => [
                        'Database\\Seeders\\Landlord\\LandlordTenantSeeder',
                        'Database\\Seeders\\Landlord\\AllCredentialsSeeder',
                    ]
                ],
                'Geography' => [
                    'description' => 'Countries, provinces, cities, towns, and streets',
                    'seeders' => [
                        'Modules\\Geography\\Database\\Seeders\\CountrySeeder',
                        'Modules\\Geography\\Database\\Seeders\\ProvinceSeeder',
                        'Modules\\Geography\\Database\\Seeders\\CitySeeder',
                        'Modules\\Geography\\Database\\Seeders\\TownSeeder',
                        'Modules\\Geography\\Database\\Seeders\\StreetSeeder',
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
