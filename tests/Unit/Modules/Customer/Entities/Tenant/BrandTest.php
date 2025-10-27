<?php

namespace Tests\Unit\Modules\Customer\Entities\Tenant;

use Tests\TestCase;
use Modules\Customer\Entities\Tenant\Brand;
use Modules\Tenant\Entities\Tenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BrandTest extends TestCase
{
    use RefreshDatabase;

    protected $tenant;
    protected $brand;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a test tenant
        $this->tenant = Tenant::on('landlord')->create([
            'name' => 'Test Tenant',
            'domain' => 'test-tenant',
            'database' => 'saas_test_tenant',
        ]);
        
        // Set tenant database connection
        Config::set('database.connections.tenant.database', $this->tenant->database);
        DB::purge('tenant');
        Config::set('database.default', 'tenant');
        
        // Make tenant current
        $this->tenant->makeCurrent();
        
        // Create test brand
        $this->brand = Brand::create([
            'name' => 'Test Brand',
            'slug' => 'test-brand',
            'description' => 'Test brand description',
            'status' => 'active',
        ]);
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
    public function it_can_create_a_brand()
    {
        $brand = Brand::create([
            'name' => 'New Brand',
            'slug' => 'new-brand',
            'description' => 'New brand description',
            'status' => 'active',
        ]);

        $this->assertInstanceOf(Brand::class, $brand);
        $this->assertEquals('New Brand', $brand->name);
        $this->assertEquals('new-brand', $brand->slug);
        $this->assertEquals('active', $brand->status);
    }

    /** @test */
    public function it_auto_generates_slug_from_name()
    {
        $brand = Brand::create([
            'name' => 'Auto Generated Slug',
            'description' => 'Test description',
            'status' => 'active',
        ]);

        $this->assertEquals('auto-generated-slug', $brand->slug);
    }

    /** @test */
    public function it_can_get_modules_count()
    {
        // Create some modules in landlord database
        $module1 = DB::connection('landlord')->table('modules')->insertGetId([
            'module_key' => 'test-module-1',
            'name' => 'Test Module 1',
            'description' => 'Test module 1',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $module2 = DB::connection('landlord')->table('modules')->insertGetId([
            'module_key' => 'test-module-2',
            'name' => 'Test Module 2',
            'description' => 'Test module 2',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Assign modules to brand
        DB::table('brand_module')->insert([
            ['brand_id' => $this->brand->id, 'module_id' => $module1, 'created_at' => now(), 'updated_at' => now()],
            ['brand_id' => $this->brand->id, 'module_id' => $module2, 'created_at' => now(), 'updated_at' => now()],
        ]);

        $this->assertEquals(2, $this->brand->modules_count);
    }

    /** @test */
    public function it_can_get_assigned_modules()
    {
        // Create modules in landlord database
        $module1 = DB::connection('landlord')->table('modules')->insertGetId([
            'module_key' => 'test-module-1',
            'name' => 'Test Module 1',
            'description' => 'Test module 1',
            'icon' => 'fas fa-test',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $module2 = DB::connection('landlord')->table('modules')->insertGetId([
            'module_key' => 'test-module-2',
            'name' => 'Test Module 2',
            'description' => 'Test module 2',
            'icon' => 'fas fa-test',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Assign modules to brand
        DB::table('brand_module')->insert([
            ['brand_id' => $this->brand->id, 'module_id' => $module1, 'created_at' => now(), 'updated_at' => now()],
            ['brand_id' => $this->brand->id, 'module_id' => $module2, 'created_at' => now(), 'updated_at' => now()],
        ]);

        $modules = $this->brand->getAssignedModules();

        $this->assertCount(2, $modules);
        $this->assertEquals('Test Module 1', $modules->first()->name);
        $this->assertEquals('test-module-1', $modules->first()->module_key);
    }

    /** @test */
    public function it_returns_empty_collection_when_no_modules_assigned()
    {
        $modules = $this->brand->getAssignedModules();
        
        $this->assertTrue($modules->isEmpty());
    }

    /** @test */
    public function it_can_search_brands()
    {
        Brand::create([
            'name' => 'Searchable Brand',
            'slug' => 'searchable-brand',
            'description' => 'This brand is searchable',
            'status' => 'active',
        ]);

        $results = Brand::search('Searchable')->get();
        
        $this->assertCount(1, $results);
        $this->assertEquals('Searchable Brand', $results->first()->name);
    }

    /** @test */
    public function it_has_logo_url_attribute()
    {
        $this->brand->logo = 'brands/test-logo.png';
        $this->brand->save();

        $this->assertStringContains('storage/brands/test-logo.png', $this->brand->logo_url);
    }

    /** @test */
    public function it_returns_placeholder_when_no_logo()
    {
        $this->assertStringContains('placeholder-brand.png', $this->brand->logo_url);
    }

    /** @test */
    public function it_can_check_module_access()
    {
        // Create module in landlord database
        $moduleId = DB::connection('landlord')->table('modules')->insertGetId([
            'module_key' => 'test-module',
            'name' => 'Test Module',
            'description' => 'Test module',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Assign module to brand
        DB::table('brand_module')->insert([
            'brand_id' => $this->brand->id,
            'module_id' => $moduleId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->assertTrue($this->brand->hasModuleAccess('test-module'));
        $this->assertFalse($this->brand->hasModuleAccess('non-existent-module'));
    }
}
