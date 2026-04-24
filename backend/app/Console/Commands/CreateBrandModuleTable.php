<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Tenant\Entities\Tenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class CreateBrandModuleTable extends Command
{
    protected $signature = 'create:brand-module-table {tenant}';
    protected $description = 'Create brand_module table for tenant';

    public function handle()
    {
        $tenantId = $this->argument('tenant');
        
        $this->info("Creating brand_module table for tenant: {$tenantId}");
        
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
            
            // Check if table exists
            $tableExists = DB::getSchemaBuilder()->hasTable('brand_module');
            if ($tableExists) {
                $this->info("✅ brand_module table already exists!");
                return;
            }
            
            // Create the table
            $this->info("Creating brand_module table...");
            DB::getSchemaBuilder()->create('brand_module', function ($table) {
                $table->id();
                $table->foreignId('brand_id')->constrained('brands')->onDelete('cascade');
                $table->unsignedBigInteger('module_id'); // No foreign key constraint since modules table is in landlord DB
                $table->timestamps();

                // Ensure unique brand-module combinations
                $table->unique(['brand_id', 'module_id']);
                
                // Indexes for performance
                $table->index('brand_id');
                $table->index('module_id');
            });
            
            $this->info("✅ brand_module table created successfully!");
            
        } catch (\Exception $e) {
            $this->error("❌ Failed to create table: " . $e->getMessage());
            $this->error("File: " . $e->getFile() . ":" . $e->getLine());
        }
    }
}
