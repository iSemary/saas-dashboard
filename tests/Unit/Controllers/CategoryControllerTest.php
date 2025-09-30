<?php

namespace Tests\Unit\Controllers;

use Tests\Unit\BaseControllerTest;
use Modules\Utilities\Http\Controllers\CategoryController;
use Modules\Utilities\Services\CategoryService;
use Illuminate\Http\Request;
use Mockery;

class CategoryControllerTest extends BaseControllerTest
{
    protected string $controllerClass = CategoryController::class;
    protected string $serviceClass = CategoryService::class;
    protected string $modelClass = \Modules\Utilities\Entities\Category::class;

    protected array $sampleData = [
        'name' => 'Test Category',
        'slug' => 'test-category',
        'description' => 'Test category description',
        'status' => 'active',
        'priority' => 1,
    ];

    /**
     * Test that the controller can be instantiated
     */
    public function test_can_be_instantiated(): void
    {
        $controller = new CategoryController($this->mockService);
        $this->assertInstanceOf(CategoryController::class, $controller);
    }

    /**
     * Test index method returns view for non-AJAX requests
     */
    public function test_index_returns_view_for_non_ajax_requests(): void
    {
        $this->mockService
            ->shouldReceive('getDataTables')
            ->never();
        
        $controller = new CategoryController($this->mockService);
        
        $request = Request::create('/categories', 'GET');
        $request->headers->set('Accept', 'text/html');
        
        $response = $controller->index();
        
        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
    }

    /**
     * Test index method returns JSON for AJAX requests
     */
    public function test_index_returns_json_for_ajax_requests(): void
    {
        $expectedData = ['data' => 'test'];
        
        $this->mockService
            ->shouldReceive('getDataTables')
            ->once()
            ->andReturn($expectedData);
        
        $controller = new CategoryController($this->mockService);
        
        $request = Request::create('/categories', 'GET');
        $request->headers->set('X-Requested-With', 'XMLHttpRequest');
        
        $response = $controller->index();
        
        $this->assertInstanceOf(\Illuminate\Http\JsonResponse::class, $response);
    }

    /**
     * Test create method returns view
     */
    public function test_create_returns_view(): void
    {
        $mockCategories = collect([
            (object)['id' => 1, 'name' => 'Parent Category'],
        ]);
        
        $this->mockService
            ->shouldReceive('getAll')
            ->once()
            ->andReturn($mockCategories);
        
        $controller = new CategoryController($this->mockService);
        
        $response = $controller->create();
        
        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
    }

    /**
     * Test store method creates category successfully
     */
    public function test_store_creates_category_successfully(): void
    {
        $this->mockService
            ->shouldReceive('create')
            ->once()
            ->with($this->sampleData)
            ->andReturn(true);
        
        $controller = new CategoryController($this->mockService);
        
        $request = Request::create('/categories', 'POST', $this->sampleData);
        
        $response = $controller->store($request);
        
        $this->assertInstanceOf(\Illuminate\Http\JsonResponse::class, $response);
        $responseData = json_decode($response->getContent(), true);
        $this->assertTrue($responseData['success']);
    }

    /**
     * Test store method handles validation errors
     */
    public function test_store_handles_validation_errors(): void
    {
        $this->mockService
            ->shouldReceive('create')
            ->once()
            ->andThrow(new \Exception('Validation failed'));
        
        $controller = new CategoryController($this->mockService);
        
        $request = Request::create('/categories', 'POST', $this->sampleData);
        
        $response = $controller->store($request);
        
        $this->assertInstanceOf(\Illuminate\Http\JsonResponse::class, $response);
        $responseData = json_decode($response->getContent(), true);
        $this->assertFalse($responseData['success']);
    }

    /**
     * Test edit method returns view for existing category
     */
    public function test_edit_returns_view_for_existing_category(): void
    {
        $mockCategory = Mockery::mock(\Modules\Utilities\Entities\Category::class);
        $mockCategories = collect([
            (object)['id' => 1, 'name' => 'Parent Category'],
        ]);
        
        $this->mockService
            ->shouldReceive('get')
            ->once()
            ->with(1)
            ->andReturn($mockCategory);
        
        $this->mockService
            ->shouldReceive('getAll')
            ->once()
            ->andReturn($mockCategories);
        
        $controller = new CategoryController($this->mockService);
        
        $response = $controller->edit(1);
        
        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
    }

    /**
     * Test update method updates category successfully
     */
    public function test_update_updates_category_successfully(): void
    {
        $this->mockService
            ->shouldReceive('update')
            ->once()
            ->with(1, $this->sampleData)
            ->andReturn(true);
        
        $controller = new CategoryController($this->mockService);
        
        $request = Request::create('/categories/1', 'PUT', $this->sampleData);
        
        $response = $controller->update($request, 1);
        
        $this->assertInstanceOf(\Illuminate\Http\JsonResponse::class, $response);
        $responseData = json_decode($response->getContent(), true);
        $this->assertTrue($responseData['success']);
    }

    /**
     * Test destroy method deletes category successfully
     */
    public function test_destroy_deletes_category_successfully(): void
    {
        $this->mockService
            ->shouldReceive('delete')
            ->once()
            ->with(1)
            ->andReturn(true);
        
        $controller = new CategoryController($this->mockService);
        
        $response = $controller->destroy(1);
        
        $this->assertInstanceOf(\Illuminate\Http\JsonResponse::class, $response);
        $responseData = json_decode($response->getContent(), true);
        $this->assertTrue($responseData['success']);
    }

    /**
     * Test restore method restores category successfully
     */
    public function test_restore_restores_category_successfully(): void
    {
        $this->mockService
            ->shouldReceive('restore')
            ->once()
            ->with(1)
            ->andReturn(true);
        
        $controller = new CategoryController($this->mockService);
        
        $response = $controller->restore(1);
        
        $this->assertInstanceOf(\Illuminate\Http\JsonResponse::class, $response);
        $responseData = json_decode($response->getContent(), true);
        $this->assertTrue($responseData['success']);
    }

    /**
     * Test middleware configuration
     */
    public function test_middleware_configuration(): void
    {
        $middleware = CategoryController::middleware();
        
        $this->assertIsArray($middleware);
        $this->assertNotEmpty($middleware);
        
        // Check for specific middleware
        $middlewareNames = array_map(function($middleware) {
            return $middleware->middleware;
        }, $middleware);
        
        $this->assertContains('permission:read.categories', $middlewareNames);
        $this->assertContains('permission:create.categories', $middlewareNames);
        $this->assertContains('permission:update.categories', $middlewareNames);
        $this->assertContains('permission:delete.categories', $middlewareNames);
        $this->assertContains('permission:restore.categories', $middlewareNames);
    }
}
