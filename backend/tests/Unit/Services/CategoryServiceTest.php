<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use Modules\Utilities\Services\CategoryService;
use Modules\Utilities\Repositories\CategoryInterface;
use Modules\Utilities\Entities\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;

class CategoryServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $mockRepository;
    protected $mockModel;
    protected $categoryService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockRepository = Mockery::mock(CategoryInterface::class);
        $this->mockModel = Mockery::mock(Category::class);
        $this->categoryService = new CategoryService($this->mockRepository, $this->mockModel);
    }

    /**
     * Test that the service can be instantiated
     */
    public function test_can_be_instantiated(): void
    {
        $this->assertInstanceOf(CategoryService::class, $this->categoryService);
    }

    /**
     * Test getAll method
     */
    public function test_get_all(): void
    {
        $expectedData = collect([
            (object)['id' => 1, 'name' => 'Category 1'],
            (object)['id' => 2, 'name' => 'Category 2'],
        ]);

        $this->mockRepository
            ->shouldReceive('all')
            ->once()
            ->andReturn($expectedData);

        $result = $this->categoryService->getAll();

        $this->assertEquals($expectedData, $result);
    }
    
    /**
     * Test get method
     */
    public function test_get(): void
    {
        $id = 1;
        $expectedResult = (object)['id' => 1, 'name' => 'Test Category'];

        $this->mockRepository
            ->shouldReceive('find')
            ->once()
            ->with($id)
            ->andReturn($expectedResult);

        $result = $this->categoryService->get($id);

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Test create method
     */
    public function test_create(): void
    {
        $data = [
            'name' => 'Test Category',
            'slug' => 'test-category',
            'description' => 'Test description',
            'status' => 'active',
        ];

        $expectedResult = (object)['id' => 1, 'name' => 'Test Category'];

        $this->mockRepository
            ->shouldReceive('create')
            ->once()
            ->with($data)
            ->andReturn($expectedResult);

        $result = $this->categoryService->create($data);

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Test update method
     */
    public function test_update(): void
    {
        $id = 1;
        $data = [
            'name' => 'Updated Category',
            'slug' => 'updated-category',
        ];

        $expectedResult = (object)['id' => 1, 'name' => 'Updated Category'];

        $this->mockRepository
            ->shouldReceive('update')
            ->once()
            ->with($id, $data)
            ->andReturn($expectedResult);

        $result = $this->categoryService->update($id, $data);

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Test delete method
     */
    public function test_delete(): void
    {
        $id = 1;
        $expectedResult = true;

        $this->mockRepository
            ->shouldReceive('delete')
            ->once()
            ->with($id)
            ->andReturn($expectedResult);

        $result = $this->categoryService->delete($id);

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Test restore method
     */
    public function test_restore(): void
    {
        $id = 1;
        $expectedResult = true;

        $this->mockRepository
            ->shouldReceive('restore')
            ->once()
            ->with($id)
            ->andReturn($expectedResult);

        $result = $this->categoryService->restore($id);

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Test create method with invalid data
     */
    public function test_create_with_invalid_data(): void
    {
        $data = [
            'name' => '',
            'slug' => '',
        ];

        $this->mockRepository
            ->shouldReceive('create')
            ->once()
            ->with($data)
            ->andThrow(new \Exception('Validation failed'));

        $this->expectException(\Exception::class);

        $this->categoryService->create($data);
    }

    /**
     * Test update method with non-existent ID
     */
    public function test_update_with_non_existent_id(): void
    {
        $id = 999;
        $data = ['name' => 'Updated Category'];

        $this->mockRepository
            ->shouldReceive('update')
            ->once()
            ->with($id, $data)
            ->andThrow(new \Exception('Category not found'));

        $this->expectException(\Exception::class);

        $this->categoryService->update($id, $data);
    }

    /**
     * Test delete method with non-existent ID
     */
    public function test_delete_with_non_existent_id(): void
    {
        $id = 999;

        $this->mockRepository
            ->shouldReceive('delete')
            ->once()
            ->with($id)
            ->andThrow(new \Exception('Category not found'));

        $this->expectException(\Exception::class);

        $this->categoryService->delete($id);
    }

    /**
     * Test get method with non-existent ID
     */
    public function test_get_with_non_existent_id(): void
    {
        $id = 999;

        $this->mockRepository
            ->shouldReceive('find')
            ->once()
            ->with($id)
            ->andReturn(null);

        $result = $this->categoryService->get($id);

        $this->assertNull($result);
    }

    /**
     * Test getAll method returns empty collection
     */
    public function test_get_all_returns_empty_collection(): void
    {
        $expectedData = collect([]);

        $this->mockRepository
            ->shouldReceive('all')
            ->once()
            ->andReturn($expectedData);

        $result = $this->categoryService->getAll();

        $this->assertEquals($expectedData, $result);
        $this->assertTrue($result->isEmpty());
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
