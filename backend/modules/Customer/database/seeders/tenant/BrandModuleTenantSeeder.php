<?php

namespace Modules\Customer\Database\Seeders\Tenant;

use Illuminate\Database\Seeder;
use Modules\Customer\Entities\Tenant\Brand;
use Modules\Customer\Entities\Tenant\BrandModule;
use Modules\Utilities\Entities\Module;
use Illuminate\Support\Facades\DB;

class BrandModuleTenantSeeder extends Seeder
{
    /**
     * Module color palettes (primary, secondary, accent + chart colors).
     */
    private const MODULE_PALETTES = [
        'crm' => [
            'primary' => '#3b82f6',
            'secondary' => '#1e40af',
            'accent' => '#60a5fa',
            'chart' => ['#3b82f6', '#60a5fa', '#93c5fd', '#1e40af', '#2563eb', '#dbeafe'],
        ],
        'hr' => [
            'primary' => '#8b5cf6',
            'secondary' => '#5b21b6',
            'accent' => '#a78bfa',
            'chart' => ['#8b5cf6', '#a78bfa', '#c4b5fd', '#5b21b6', '#7c3aed', '#ede9fe'],
        ],
        'pos' => [
            'primary' => '#f97316',
            'secondary' => '#c2410c',
            'accent' => '#fb923c',
            'chart' => ['#f97316', '#fb923c', '#fdba74', '#c2410c', '#ea580c', '#fff7ed'],
        ],
        'survey' => [
            'primary' => '#06b6d4',
            'secondary' => '#0e7490',
            'accent' => '#22d3ee',
            'chart' => ['#06b6d4', '#22d3ee', '#67e8f9', '#0e7490', '#0891b2', '#cffafe'],
        ],
        'events' => [
            'primary' => '#ec4899',
            'secondary' => '#be185d',
            'accent' => '#f472b6',
            'chart' => ['#ec4899', '#f472b6', '#f9a8d4', '#be185d', '#db2777', '#fce7f3'],
        ],
        'inventory' => [
            'primary' => '#14b8a6',
            'secondary' => '#0f766e',
            'accent' => '#2dd4bf',
            'chart' => ['#14b8a6', '#2dd4bf', '#5eead4', '#0f766e', '#0d9488', '#ccfbf1'],
        ],
        'accounting' => [
            'primary' => '#22c55e',
            'secondary' => '#15803d',
            'accent' => '#4ade80',
            'chart' => ['#22c55e', '#4ade80', '#86efac', '#15803d', '#16a34a', '#dcfce7'],
        ],
        'expenses' => [
            'primary' => '#ef4444',
            'secondary' => '#b91c1c',
            'accent' => '#f87171',
            'chart' => ['#ef4444', '#f87171', '#fca5a5', '#b91c1c', '#dc2626', '#fee2e2'],
        ],
    ];

    public function run(): void
    {
        $this->command->info('Seeding brand modules (tenant)...');

        $brands = Brand::all();

        if ($brands->isEmpty()) {
            $this->command->warn('No brands found. Please run BrandSeeder first.');
            return;
        }

        // Get active modules from landlord database
        $modules = Module::on('landlord')->where('status', 'active')->get();

        if ($modules->isEmpty()) {
            $this->command->warn('No modules found in landlord database.');
            return;
        }

        // Only seed modules that have frontend pages (crm, hr, pos)
        $supportedKeys = ['crm', 'hr', 'pos'];
        $filteredModules = $modules->filter(fn($m) => in_array($m->module_key, $supportedKeys));

        if ($filteredModules->isEmpty()) {
            $this->command->warn('No supported modules found.');
            return;
        }

        foreach ($brands as $brand) {
            // Assign 2-3 random modules to each brand
            $randomModules = $filteredModules->random(rand(2, min(3, $filteredModules->count())));

            foreach ($randomModules as $module) {
                BrandModule::updateOrCreate(
                    [
                        'brand_id' => $brand->id,
                        'module_id' => $module->id,
                    ],
                    [
                        'module_key' => $module->module_key,
                        'status' => 'active',
                        'color_palette' => self::MODULE_PALETTES[$module->module_key] ?? null,
                        'subscribed_at' => now(),
                    ]
                );
            }

            $this->command->info("Assigned {$randomModules->count()} modules to brand: {$brand->name}");
        }

        $this->command->info('✅ Brand modules seeded successfully!');
    }
}
