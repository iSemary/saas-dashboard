<?php

namespace Modules\Customer\Tests\Feature;

use Modules\Customer\Entities\Brand;
use Modules\Customer\Services\BrandService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\Auth\Entities\User;
use Modules\Tenant\Entities\Tenant;
use Tests\TestCase;

class BrandTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $tenant;
    protected $brandService;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test user and tenant
        $this->user = User::factory()->create();
        $this->tenant = Tenant::factory()->create();
        $this->brandService = app(BrandService::class);
        
        // Set up storage for testing
        Storage::fake('public');
    }

    /** @test */
    public function it_can_create_a_brand()
    {
        $brandData = [
            'name' => 'Test Brand',
            'slug' => 'test-brand',
            'description' => 'This is a test brand',
            'tenant_id' => $this->tenant->id,
            'created_by' => $this->user->id,
        ];

        $brand = Brand::create($brandData);

        $this->assertInstanceOf(Brand::class, $brand);
        $this->assertEquals('Test Brand', $brand->name);
        $this->assertEquals('test-brand', $brand->slug);
        $this->assertEquals($this->tenant->id, $brand->tenant_id);
        $this->assertEquals($this->user->id, $brand->created_by);
    }

    /** @test */
    public function it_auto_generates_slug_from_name()
    {
        $brandData = [
            'name' => 'My Awesome Brand',
            'description' => 'This is a test brand',
            'tenant_id' => $this->tenant->id,
            'created_by' => $this->user->id,
        ];

        $brand = Brand::create($brandData);

        $this->assertEquals('my-awesome-brand', $brand->slug);
    }

    /** @test */
    public function it_can_upload_logo()
    {
        $logo = UploadedFile::fake()->image('logo.jpg', 100, 100);
        
        $brandData = [
            'name' => 'Test Brand',
            'slug' => 'test-brand',
            'description' => 'This is a test brand',
            'tenant_id' => $this->tenant->id,
            'created_by' => $this->user->id,
            'logo' => $logo,
        ];

        $brand = $this->brandService->create($brandData);

        $this->assertNotNull($brand->logo);
        Storage::disk('public')->assertExists($brand->logo);
    }

    /** @test */
    public function it_can_update_a_brand()
    {
        $brand = Brand::create([
            'name' => 'Original Brand',
            'slug' => 'original-brand',
            'description' => 'Original description',
            'tenant_id' => $this->tenant->id,
            'created_by' => $this->user->id,
        ]);

        $updateData = [
            'name' => 'Updated Brand',
            'description' => 'Updated description',
        ];

        $updated = $this->brandService->update($brand->id, $updateData);

        $this->assertTrue($updated);
        
        $brand->refresh();
        $this->assertEquals('Updated Brand', $brand->name);
        $this->assertEquals('Updated description', $brand->description);
    }

    /** @test */
    public function it_can_soft_delete_a_brand()
    {
        $brand = Brand::create([
            'name' => 'Test Brand',
            'slug' => 'test-brand',
            'description' => 'This is a test brand',
            'tenant_id' => $this->tenant->id,
            'created_by' => $this->user->id,
        ]);

        $deleted = $this->brandService->delete($brand->id);

        $this->assertTrue($deleted);
        $this->assertSoftDeleted('brands', ['id' => $brand->id]);
    }

    /** @test */
    public function it_can_restore_a_soft_deleted_brand()
    {
        $brand = Brand::create([
            'name' => 'Test Brand',
            'slug' => 'test-brand',
            'description' => 'This is a test brand',
            'tenant_id' => $this->tenant->id,
            'created_by' => $this->user->id,
        ]);

        $brand->delete();
        $this->assertSoftDeleted('brands', ['id' => $brand->id]);

        $restored = $this->brandService->restore($brand->id);

        $this->assertTrue($restored);
        $this->assertDatabaseHas('brands', [
            'id' => $brand->id,
            'deleted_at' => null,
        ]);
    }

    /** @test */
    public function it_can_search_brands()
    {
        Brand::create([
            'name' => 'Tech Brand',
            'slug' => 'tech-brand',
            'description' => 'Technology company',
            'tenant_id' => $this->tenant->id,
            'created_by' => $this->user->id,
        ]);

        Brand::create([
            'name' => 'Food Brand',
            'slug' => 'food-brand',
            'description' => 'Food company',
            'tenant_id' => $this->tenant->id,
            'created_by' => $this->user->id,
        ]);

        $results = $this->brandService->search('tech');

        $this->assertCount(1, $results);
        $this->assertEquals('Tech Brand', $results->first()->name);
    }

    /** @test */
    public function it_can_filter_brands_by_tenant()
    {
        $tenant2 = Tenant::factory()->create();

        Brand::create([
            'name' => 'Brand 1',
            'slug' => 'brand-1',
            'description' => 'Brand for tenant 1',
            'tenant_id' => $this->tenant->id,
            'created_by' => $this->user->id,
        ]);

        Brand::create([
            'name' => 'Brand 2',
            'slug' => 'brand-2',
            'description' => 'Brand for tenant 2',
            'tenant_id' => $tenant2->id,
            'created_by' => $this->user->id,
        ]);

        $brands = $this->brandService->getByTenant($this->tenant->id);

        $this->assertCount(1, $brands);
        $this->assertEquals('Brand 1', $brands->first()->name);
    }

    /** @test */
    public function it_generates_unique_slugs()
    {
        Brand::create([
            'name' => 'Test Brand',
            'slug' => 'test-brand',
            'description' => 'First brand',
            'tenant_id' => $this->tenant->id,
            'created_by' => $this->user->id,
        ]);

        $uniqueSlug = $this->brandService->generateUniqueSlug('Test Brand', $this->tenant->id);

        $this->assertEquals('test-brand-1', $uniqueSlug);
    }

    /** @test */
    public function it_validates_slug_uniqueness()
    {
        Brand::create([
            'name' => 'Test Brand',
            'slug' => 'test-brand',
            'description' => 'First brand',
            'tenant_id' => $this->tenant->id,
            'created_by' => $this->user->id,
        ]);

        $isUnique = $this->brandService->isSlugUnique('test-brand', $this->tenant->id);
        $this->assertFalse($isUnique);

        $isUnique = $this->brandService->isSlugUnique('different-brand', $this->tenant->id);
        $this->assertTrue($isUnique);
    }

    /** @test */
    public function it_can_get_dashboard_stats()
    {
        Brand::create([
            'name' => 'Brand 1',
            'slug' => 'brand-1',
            'description' => 'First brand',
            'tenant_id' => $this->tenant->id,
            'created_by' => $this->user->id,
        ]);

        Brand::create([
            'name' => 'Brand 2',
            'slug' => 'brand-2',
            'description' => 'Second brand',
            'tenant_id' => $this->tenant->id,
            'created_by' => $this->user->id,
        ]);

        $stats = $this->brandService->getDashboardStats();

        $this->assertArrayHasKey('total', $stats);
        $this->assertArrayHasKey('active', $stats);
        $this->assertArrayHasKey('deleted', $stats);
        $this->assertArrayHasKey('recent_30_days', $stats);
        $this->assertArrayHasKey('by_tenant', $stats);
        
        $this->assertEquals(2, $stats['total']);
        $this->assertEquals(2, $stats['active']);
        $this->assertEquals(0, $stats['deleted']);
    }

    /** @test */
    public function it_has_tenant_relationship()
    {
        $brand = Brand::create([
            'name' => 'Test Brand',
            'slug' => 'test-brand',
            'description' => 'This is a test brand',
            'tenant_id' => $this->tenant->id,
            'created_by' => $this->user->id,
        ]);

        $this->assertInstanceOf(Tenant::class, $brand->tenant);
        $this->assertEquals($this->tenant->id, $brand->tenant->id);
    }

    /** @test */
    public function it_has_creator_relationship()
    {
        $brand = Brand::create([
            'name' => 'Test Brand',
            'slug' => 'test-brand',
            'description' => 'This is a test brand',
            'tenant_id' => $this->tenant->id,
            'created_by' => $this->user->id,
        ]);

        $this->assertInstanceOf(User::class, $brand->creator);
        $this->assertEquals($this->user->id, $brand->creator->id);
    }

    /** @test */
    public function it_can_get_logo_url()
    {
        $brand = Brand::create([
            'name' => 'Test Brand',
            'slug' => 'test-brand',
            'description' => 'This is a test brand',
            'tenant_id' => $this->tenant->id,
            'created_by' => $this->user->id,
            'logo' => 'brands/logos/test-logo.jpg',
        ]);

        $logoUrl = $brand->logo_url;
        $this->assertStringContains('storage/brands/logos/test-logo.jpg', $logoUrl);
    }

    /** @test */
    public function it_returns_null_logo_url_when_no_logo()
    {
        $brand = Brand::create([
            'name' => 'Test Brand',
            'slug' => 'test-brand',
            'description' => 'This is a test brand',
            'tenant_id' => $this->tenant->id,
            'created_by' => $this->user->id,
        ]);

        $this->assertNull($brand->logo_url);
    }
}
