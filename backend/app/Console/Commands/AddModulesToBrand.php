<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\Customer\Entities\Tenant\Brand;
use Modules\Customer\Entities\Tenant\BrandModule;

class AddModulesToBrand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'modules:add-to-brand {--brand-id= : Specific brand ID} {--modules=hr,pos,survey : Comma-separated module keys} {--tenant-database= : Tenant database name (e.g., saas_customer1)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add HR, POS, Survey modules to a brand';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Adding modules to brand...');

        // Get the tenant database name
        $tenantDatabase = $this->option('tenant-database');
        if (!$tenantDatabase) {
            $this->error('Please specify --tenant-database (e.g., saas_customer1)');
            return 1;
        }

        // Create a dynamic connection to the tenant database
        config(["database.connections.tenant_dynamic" => [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => $tenantDatabase,
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
        ]]);

        // Get the brand from tenant database
        $brandId = $this->option('brand-id');
        if ($brandId) {
            $brand = DB::connection('tenant_dynamic')->table('brands')->where('id', $brandId)->first();
        } else {
            $brand = DB::connection('tenant_dynamic')->table('brands')->first();
        }

        if (!$brand) {
            $this->error("No brand found in tenant database: {$tenantDatabase}");
            return 1;
        }

        $this->info("Processing brand: {$brand->name} (ID: {$brand->id}) in database: {$tenantDatabase}");

        // Get module keys
        $moduleKeys = explode(',', $this->option('modules'));
        $moduleKeys = array_map('trim', $moduleKeys);

        // Get the module IDs from landlord database
        $modules = DB::connection('landlord')
            ->table('modules')
            ->whereIn('module_key', $moduleKeys)
            ->where('status', 'active')
            ->get();

        if ($modules->isEmpty()) {
            $this->warn('No matching modules found in landlord database.');
            return 1;
        }

        $this->info("Found {$modules->count()} modules in landlord database");

        // Add each module to the brand in tenant database
        foreach ($modules as $module) {
            $existing = DB::connection('tenant_dynamic')->table('brand_modules')
                ->where('brand_id', $brand->id)
                ->where('module_id', $module->id)
                ->first();

            if ($existing) {
                $status = $existing->status ?? 'N/A';
                $this->warn("Module {$module->module_key} is already subscribed (status: {$status})");

                if ($existing->status !== 'active' || (isset($existing->deleted_at) && $existing->deleted_at !== null)) {
                    $updateData = ['status' => 'active'];
                    if (isset($existing->deleted_at)) {
                        $updateData['deleted_at'] = null;
                    }
                    DB::connection('tenant_dynamic')->table('brand_modules')
                        ->where('id', $existing->id)
                        ->update($updateData);
                    $this->info("✅ Activated and restored module {$module->module_key}");
                }
                continue;
            }

            DB::connection('tenant_dynamic')->table('brand_modules')->insert([
                'brand_id' => $brand->id,
                'module_id' => $module->id,
                'module_key' => $module->module_key,
                'status' => 'active',
                'subscribed_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->info("✅ Subscribed brand to module: {$module->module_key} ({$module->name})");
        }

        $this->info('✅ Modules added successfully!');
        return 0;
    }
}
