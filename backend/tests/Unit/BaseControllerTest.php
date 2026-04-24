<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Mockery;

/**
 * Base test class for controller testing
 * Provides common test methods and assertions for all controllers
 */
abstract class BaseControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * The controller class being tested
     */
    protected string $controllerClass;

    /**
     * The service class being mocked
     */
    protected string $serviceClass;

    /**
     * The model class being tested
     */
    protected string $modelClass;

    /**
     * Mock service instance
     */
    protected $mockService;

    /**
     * Sample data for testing
     */
    protected array $sampleData = [];

    /**
     * Set up the test environment
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Create mock service
        $this->mockService = Mockery::mock($this->serviceClass);
        $this->app->instance($this->serviceClass, $this->mockService);
    }

    /**
     * Test that the controller can be instantiated
     */
    public function test_can_be_instantiated(): void
    {
        $controller = new $this->controllerClass($this->mockService);
        $this->assertInstanceOf($this->controllerClass, $controller);
    }

    /**
     * Test index method returns view for non-AJAX requests
     */
    public function test_index_returns_view_for_non_ajax_requests(): void
    {
        $controller = new $this->controllerClass($this->mockService);
        
        $request = Request::create('/test', 'GET');
        $request->headers->set('Accept', 'text/html');
        
        $response = $controller->index();
        
        $this->assertInstanceOf(View::class, $response);
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
        
        $controller = new $this->controllerClass($this->mockService);
        
        $request = Request::create('/test', 'GET');
        $request->headers->set('X-Requested-With', 'XMLHttpRequest');
        
        $response = $controller->index();
        
        $this->assertInstanceOf(JsonResponse::class, $response);
    }

    /**
     * Test create method returns view
     */
    public function test_create_returns_view(): void
    {
        $controller = new $this->controllerClass($this->mockService);
        
        $response = $controller->create();
        
        $this->assertInstanceOf(View::class, $response);
    }

    /**
     * Test store method creates resource successfully
     */
    public function test_store_creates_resource_successfully(): void
    {
        $this->mockService
            ->shouldReceive('create')
            ->once()
            ->with($this->sampleData)
            ->andReturn(true);
        
        $controller = new $this->controllerClass($this->mockService);
        
        $request = Request::create('/test', 'POST', $this->sampleData);
        
        $response = $controller->store($request);
        
        $this->assertInstanceOf(JsonResponse::class, $response);
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
        
        $controller = new $this->controllerClass($this->mockService);
        
        $request = Request::create('/test', 'POST', $this->sampleData);
        
        $response = $controller->store($request);
        
        $this->assertInstanceOf(JsonResponse::class, $response);
        $responseData = json_decode($response->getContent(), true);
        $this->assertFalse($responseData['success']);
    }

    /**
     * Test show method returns view for existing resource
     */
    public function test_show_returns_view_for_existing_resource(): void
    {
        $mockModel = Mockery::mock($this->modelClass);
        
        $this->mockService
            ->shouldReceive('get')
            ->once()
            ->with(1)
            ->andReturn($mockModel);
        
        $controller = new $this->controllerClass($this->mockService);
        
        $response = $controller->show(1);
        
        $this->assertInstanceOf(View::class, $response);
    }

    /**
     * Test show method returns 404 for non-existing resource
     */
    public function test_show_returns_404_for_non_existing_resource(): void
    {
        $this->mockService
            ->shouldReceive('get')
            ->once()
            ->with(999)
            ->andReturn(null);
        
        $controller = new $this->controllerClass($this->mockService);
        
        $response = $controller->show(999);
        
        $this->assertInstanceOf(JsonResponse::class, $response);
        $responseData = json_decode($response->getContent(), true);
        $this->assertFalse($responseData['success']);
    }

    /**
     * Test edit method returns view for existing resource
     */
    public function test_edit_returns_view_for_existing_resource(): void
    {
        $mockModel = Mockery::mock($this->modelClass);
        
        $this->mockService
            ->shouldReceive('get')
            ->once()
            ->with(1)
            ->andReturn($mockModel);
        
        $controller = new $this->controllerClass($this->mockService);
        
        $response = $controller->edit(1);
        
        $this->assertInstanceOf(View::class, $response);
    }

    /**
     * Test update method updates resource successfully
     */
    public function test_update_updates_resource_successfully(): void
    {
        $this->mockService
            ->shouldReceive('update')
            ->once()
            ->with(1, $this->sampleData)
            ->andReturn(true);
        
        $controller = new $this->controllerClass($this->mockService);
        
        $request = Request::create('/test/1', 'PUT', $this->sampleData);
        
        $response = $controller->update($request, 1);
        
        $this->assertInstanceOf(JsonResponse::class, $response);
        $responseData = json_decode($response->getContent(), true);
        $this->assertTrue($responseData['success']);
    }

    /**
     * Test destroy method deletes resource successfully
     */
    public function test_destroy_deletes_resource_successfully(): void
    {
        $this->mockService
            ->shouldReceive('delete')
            ->once()
            ->with(1)
            ->andReturn(true);
        
        $controller = new $this->controllerClass($this->mockService);
        
        $response = $controller->destroy(1);
        
        $this->assertInstanceOf(JsonResponse::class, $response);
        $responseData = json_decode($response->getContent(), true);
        $this->assertTrue($responseData['success']);
    }

    /**
     * Test restore method restores resource successfully
     */
    public function test_restore_restores_resource_successfully(): void
    {
        $this->mockService
            ->shouldReceive('restore')
            ->once()
            ->with(1)
            ->andReturn(true);
        
        $controller = new $this->controllerClass($this->mockService);
        
        $response = $controller->restore(1);
        
        $this->assertInstanceOf(JsonResponse::class, $response);
        $responseData = json_decode($response->getContent(), true);
        $this->assertTrue($responseData['success']);
    }

    /**
     * Test middleware configuration
     */
    public function test_middleware_configuration(): void
    {
        $middleware = $this->controllerClass::middleware();
        
        $this->assertIsArray($middleware);
        $this->assertNotEmpty($middleware);
    }

    /**
     * Clean up after tests
     */
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
