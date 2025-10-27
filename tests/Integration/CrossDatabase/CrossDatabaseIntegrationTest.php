<?php

namespace Tests\Integration\CrossDatabase;

use Tests\TestCase;
use Modules\Customer\Entities\Tenant\Brand;
use Modules\Tenant\Entities\Tenant;
use App\Services\CrossDatabaseService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CrossDatabaseIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected $tenant;
    protected $crossDbService;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a test tenant
        $this->tenant = Tenant::on('landlord')->create([
            'name' => 'Integration Test Tenant',
            'domain' => 'integration-test',
            'database' => 'saas_integration_test',
        ]);
        
        // Set tenant database connection
        Config::set('database.connections.tenant.database', $this->tenant->database);
        DB::purge('tenant');
        Config::set('database.default', 'tenant');
        
        // Make tenant current
        $this->tenant->makeCurrent();
        
        // Create brand_module table
        DB::getSchemaBuilder()->create('brand_module', function ($table) {
            $table->id();
            $table->foreignId('brand_id')->constrained('brands')->onDelete('cascade');
            $table->unsignedBigInteger('module_id');
            $table->timestamps();
            $table->unique(['brand_id', 'module_id']);
            $table->index('brand_id');
            $table->index('module_id');
        });
        
        $this->crossDbService = app(CrossDatabaseService::class);
    }

    protected function tearDown(): void
    {
        // Clean up
        if ($this->tenant) {
            $this->tenant->delete();
        }
        
        parent::tearDown();
    }

    /** @test */
    public function it_can_get_modules_from_landlord_database()
    {
        // Create modules in landlord database
        $module1 = DB::connection('landlord')->table('modules')->insertGetId([
            'module_key' => 'integration-module-1',
            'name' => 'Integration Module 1',
            'description' => 'Test module for integration',
            'icon' => 'fas fa-test',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $module2 = DB::connection('landlord')->table('modules')->insertGetId([
            'module_key' => 'integration-module-2',
            'name' => 'Integration Module 2',
            'description' => 'Another test module',
            'icon' => 'fas fa-test',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Test getting modules by IDs
        $modules = $this->crossDbService->getFromLandlord('modules/by-ids', [
            'ids' => [$module1, $module2]
        ]);

        $this->assertNotNull($modules);
        $this->assertArrayHasKey('data', $modules);
        $this->assertCount(2, $modules['data']);
        
        $moduleNames = collect($modules['data'])->pluck('name')->toArray();
        $this->assertContains('Integration Module 1', $moduleNames);
        $this->assertContains('Integration Module 2', $moduleNames);
    }

    /** @test */
    public function it_can_get_brands_from_tenant_database()
    {
        // Create brands in tenant database
        $brand1 = Brand::create([
            'name' => 'Integration Brand 1',
            'slug' => 'integration-brand-1',
            'description' => 'Test brand for integration',
            'status' => 'active',
        ]);

        $brand2 = Brand::create([
            'name' => 'Integration Brand 2',
            'slug' => 'integration-brand-2',
            'description' => 'Another test brand',
            'status' => 'active',
        ]);

        // Test getting brands
        $brands = $this->crossDbService->getFromTenant($this->tenant->id, 'brands');

        $this->assertNotNull($brands);
        $this->assertArrayHasKey('data', $brands);
        $this->assertCount(2, $brands['data']);
        
        $brandNames = collect($brands['data'])->pluck('name')->toArray();
        $this->assertContains('Integration Brand 1', $brandNames);
        $this->assertContains('Integration Brand 2', $brandNames);
    }

    /** @test */
    public function it_can_assign_modules_to_brand()
    {
        // Create brand
        $brand = Brand::create([
            'name' => 'Test Brand for Assignment',
            'slug' => 'test-brand-assignment',
            'description' => 'Test brand',
            'status' => 'active',
        ]);

        // Create modules in landlord database
        $module1 = DB::connection('landlord')->table('modules')->insertGetId([
            'module_key' => 'assignment-module-1',
            'name' => 'Assignment Module 1',
            'description' => 'Module for assignment test',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $module2 = DB::connection('landlord')->table('modules')->insertGetId([
            'module_key' => 'assignment-module-2',
            'name' => 'Assignment Module 2',
            'description' => 'Another module for assignment',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Assign modules to brand
        $result = $this->crossDbService->assignBrandModules(
            $this->tenant->id,
            $brand->id,
            [$module1, $module2]
        );

        $this->assertNotNull($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertTrue($result['success']);

        // Verify assignment
        $assignedModules = $brand->getAssignedModules();
        $this->assertCount(2, $assignedModules);
        
        $moduleNames = $assignedModules->pluck('name')->toArray();
        $this->assertContains('Assignment Module 1', $moduleNames);
        $this->assertContains('Assignment Module 2', $moduleNames);
    }

    /** @test */
    public function it_can_get_brand_modules()
    {
        // Create brand
        $brand = Brand::create([
            'name' => 'Test Brand for Modules',
            'slug' => 'test-brand-modules',
            'description' => 'Test brand',
            'status' => 'active',
        ]);

        // Create modules in landlord database
        $module1 = DB::connection('landlord')->table('modules')->insertGetId([
            'module_key' => 'brand-module-1',
            'name' => 'Brand Module 1',
            'description' => 'Module for brand test',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $module2 = DB::connection('landlord')->table('modules')->insertGetId([
            'module_key' => 'brand-module-2',
            'name' => 'Brand Module 2',
            'description' => 'Another module for brand',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Assign modules to brand
        DB::table('brand_module')->insert([
            ['brand_id' => $brand->id, 'module_id' => $module1, 'created_at' => now(), 'updated_at' => now()],
            ['brand_id' => $brand->id, 'module_id' => $module2, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Get brand modules
        $modules = $this->crossDbService->getBrandModules($this->tenant->id, $brand->id);

        $this->assertNotNull($modules);
        $this->assertArrayHasKey('data', $modules);
        $this->assertCount(2, $modules['data']);
        
        $moduleNames = collect($modules['data'])->pluck('name')->toArray();
        $this->assertContains('Brand Module 1', $moduleNames);
        $this->assertContains('Brand Module 2', $moduleNames);
    }

    /** @test */
    public function it_handles_empty_module_assignments()
    {
        // Create brand
        $brand = Brand::create([
            'name' => 'Empty Brand',
            'slug' => 'empty-brand',
            'description' => 'Brand with no modules',
            'status' => 'active',
        ]);

        // Get brand modules (should be empty)
        $modules = $this->crossDbService->getBrandModules($this->tenant->id, $brand->id);

        $this->assertNotNull($modules);
        $this->assertArrayHasKey('data', $modules);
        $this->assertCount(0, $modules['data']);
    }

    /** @test */
    public function it_can_get_module_statistics()
    {
        // Create modules in landlord database
        DB::connection('landlord')->table('modules')->insert([
            [
                'module_key' => 'stats-module-1',
                'name' => 'Stats Module 1',
                'description' => 'Module for stats',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'module_key' => 'stats-module-2',
                'name' => 'Stats Module 2',
                'description' => 'Another module for stats',
                'status' => 'inactive',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Get module statistics
        $stats = $this->crossDbService->getFromLandlord('modules-stats');

        $this->assertNotNull($stats);
        $this->assertArrayHasKey('data', $stats);
        $this->assertArrayHasKey('total_modules', $stats['data']);
        $this->assertArrayHasKey('active_modules', $stats['data']);
        $this->assertArrayHasKey('inactive_modules', $stats['data']);
        
        $this->assertEquals(2, $stats['data']['total_modules']);
        $this->assertEquals(1, $stats['data']['active_modules']);
        $this->assertEquals(1, $stats['data']['inactive_modules']);
    }
}
