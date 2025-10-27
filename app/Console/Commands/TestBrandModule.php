<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Customer\Entities\Tenant\Brand;
use Modules\Tenant\Entities\Tenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class TestBrandModule extends Command
{
    protected $signature = 'test:brand-module {tenant}';
    protected $description = 'Test brand module functionality';

    public function handle()
    {
        $tenantId = $this->argument('tenant');
        
        $this->info("Testing brand module for tenant: {$tenantId}");
        
        try {
            // Get tenant information
            $tenant = Tenant::on('landlord')->find($tenantId);
            if (!$tenant) {
                $this->error("❌ Tenant not found!");
                return;
            }
            
            $this->info("Tenant: {$tenant->name} (Database: {$tenant->database})");
            
            // Set tenant database connection
            Config::set('database.connections.tenant.database', $tenant->database);
            DB::purge('tenant');
            Config::set('database.default', 'tenant');
            
            // Make tenant current
            $tenant->makeCurrent();
            
            // Test database connection
            $this->info("Testing tenant database connection...");
            $brandCount = DB::table('brands')->count();
            $this->info("Brands count: {$brandCount}");
            
            // Test brand_module table
            $this->info("Testing brand_module table...");
            $moduleCount = DB::table('brand_module')->count();
            $this->info("Brand modules count: {$moduleCount}");
            
            // Test landlord connection
            $this->info("Testing landlord database connection...");
            $landlordModules = DB::connection('landlord')->table('modules')->count();
            $this->info("Landlord modules count: {$landlordModules}");
            
            // Test Brand model
            $this->info("Testing Brand model...");
            $brands = Brand::all();
            $this->info("Brand model count: " . $brands->count());
            
            if ($brands->count() > 0) {
                $brand = $brands->first();
                $this->info("First brand: {$brand->name}");
                
                $modules = $brand->getAssignedModules();
                $this->info("Assigned modules count: " . $modules->count());
                
                if ($modules->count() > 0) {
                    $this->info("First module: " . $modules->first()->name);
                }
            }
            
            $this->info("✅ All tests passed!");
            
        } catch (\Exception $e) {
            $this->error("❌ Test failed: " . $e->getMessage());
            $this->error("File: " . $e->getFile() . ":" . $e->getLine());
        }
    }
}
