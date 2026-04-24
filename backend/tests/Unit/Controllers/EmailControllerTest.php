<?php

namespace Tests\Unit\Controllers;

use Tests\Unit\BaseControllerTest;
use Modules\Email\Http\Controllers\EmailController;
use Modules\Email\Services\EmailService;
use Modules\Email\Services\EmailCredentialService;
use Modules\Email\Services\EmailTemplateService;
use Illuminate\Http\Request;
use Mockery;

class EmailControllerTest extends BaseControllerTest
{
    protected string $controllerClass = EmailController::class;
    protected string $serviceClass = EmailService::class;
    protected string $modelClass = \Modules\Email\Entities\EmailLog::class;

    protected $mockEmailCredentialService;
    protected $mockEmailTemplateService;

    protected array $sampleData = [
        'email' => 'test@example.com',
        'subject' => 'Test Subject',
        'body' => 'Test Body',
        'email_credential_id' => 1,
        'email_template_id' => 1,
    ];

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create mock services
        $this->mockEmailCredentialService = Mockery::mock(EmailCredentialService::class);
        $this->mockEmailTemplateService = Mockery::mock(EmailTemplateService::class);
        
        $this->app->instance(EmailCredentialService::class, $this->mockEmailCredentialService);
        $this->app->instance(EmailTemplateService::class, $this->mockEmailTemplateService);
    }

    /**
     * Test that the controller can be instantiated with all dependencies
     */
    public function test_can_be_instantiated_with_all_dependencies(): void
    {
        $controller = new EmailController(
            $this->mockService,
            $this->mockEmailCredentialService,
            $this->mockEmailTemplateService
        );
        
        $this->assertInstanceOf(EmailController::class, $controller);
    }

    /**
     * Test index method returns view for non-AJAX requests
     */
    public function test_index_returns_view_for_non_ajax_requests(): void
    {
        $this->mockService
            ->shouldReceive('getDataTables')
            ->never();
        
        $controller = new EmailController(
            $this->mockService,
            $this->mockEmailCredentialService,
            $this->mockEmailTemplateService
        );
        
        $request = Request::create('/emails', 'GET');
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
        
        $controller = new EmailController(
            $this->mockService,
            $this->mockEmailCredentialService,
            $this->mockEmailTemplateService
        );
        
        $request = Request::create('/emails', 'GET');
        $request->headers->set('X-Requested-With', 'XMLHttpRequest');
        
        $response = $controller->index();
        
        $this->assertInstanceOf(\Illuminate\Http\JsonResponse::class, $response);
    }

    /**
     * Test show method returns view for existing email log
     */
    public function test_show_returns_view_for_existing_email_log(): void
    {
        $mockEmailLog = Mockery::mock(\Modules\Email\Entities\EmailLog::class);
        
        $this->mockService
            ->shouldReceive('getById')
            ->once()
            ->with(1)
            ->andReturn($mockEmailLog);
        
        $controller = new EmailController(
            $this->mockService,
            $this->mockEmailCredentialService,
            $this->mockEmailTemplateService
        );
        
        $response = $controller->show(1);
        
        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
    }

    /**
     * Test compose method returns view with required data
     */
    public function test_compose_returns_view_with_required_data(): void
    {
        $mockEmailCredentials = collect([
            (object)['id' => 1, 'name' => 'Test SMTP'],
            (object)['id' => 2, 'name' => 'Test SMTP 2'],
        ]);
        
        $mockEmailTemplates = collect([
            (object)['id' => 1, 'name' => 'Test Template'],
            (object)['id' => 2, 'name' => 'Test Template 2'],
        ]);
        
        $this->mockEmailCredentialService
            ->shouldReceive('getAll')
            ->once()
            ->with(['status' => 'active'])
            ->andReturn($mockEmailCredentials);
        
        $this->mockEmailTemplateService
            ->shouldReceive('getAll')
            ->once()
            ->with(['status' => 'active'])
            ->andReturn($mockEmailTemplates);
        
        $controller = new EmailController(
            $this->mockService,
            $this->mockEmailCredentialService,
            $this->mockEmailTemplateService
        );
        
        $response = $controller->compose();
        
        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
    }

    /**
     * Test send method sends email successfully
     */
    public function test_send_sends_email_successfully(): void
    {
        $requestData = $this->sampleData;
        
        $this->mockService
            ->shouldReceive('send')
            ->once()
            ->with($requestData)
            ->andReturn(['success' => true]);
        
        $controller = new EmailController(
            $this->mockService,
            $this->mockEmailCredentialService,
            $this->mockEmailTemplateService
        );
        
        $request = Request::create('/emails/send', 'POST', $requestData);
        
        $response = $controller->send($request);
        
        $this->assertInstanceOf(\Illuminate\Http\JsonResponse::class, $response);
        $responseData = json_decode($response->getContent(), true);
        $this->assertTrue($responseData['success']);
    }

    /**
     * Test send method handles email sending failure
     */
    public function test_send_handles_email_sending_failure(): void
    {
        $requestData = $this->sampleData;
        
        $this->mockService
            ->shouldReceive('send')
            ->once()
            ->with($requestData)
            ->andReturn(['success' => false, 'message' => 'SMTP connection failed']);
        
        $controller = new EmailController(
            $this->mockService,
            $this->mockEmailCredentialService,
            $this->mockEmailTemplateService
        );
        
        $request = Request::create('/emails/send', 'POST', $requestData);
        
        $response = $controller->send($request);
        
        $this->assertInstanceOf(\Illuminate\Http\JsonResponse::class, $response);
        $responseData = json_decode($response->getContent(), true);
        $this->assertFalse($responseData['success']);
    }

    /**
     * Test resend method resends email successfully
     */
    public function test_resend_resends_email_successfully(): void
    {
        $this->mockService
            ->shouldReceive('resend')
            ->once()
            ->with([1])
            ->andReturn(['success' => true]);
        
        $controller = new EmailController(
            $this->mockService,
            $this->mockEmailCredentialService,
            $this->mockEmailTemplateService
        );
        
        $response = $controller->resend(1);
        
        $this->assertInstanceOf(\Illuminate\Http\JsonResponse::class, $response);
        $responseData = json_decode($response->getContent(), true);
        $this->assertTrue($responseData['success']);
    }

    /**
     * Test resend method handles resending failure
     */
    public function test_resend_handles_resending_failure(): void
    {
        $this->mockService
            ->shouldReceive('resend')
            ->once()
            ->with([1])
            ->andReturn(['success' => false, 'message' => 'Email not found']);
        
        $controller = new EmailController(
            $this->mockService,
            $this->mockEmailCredentialService,
            $this->mockEmailTemplateService
        );
        
        $response = $controller->resend(1);
        
        $this->assertInstanceOf(\Illuminate\Http\JsonResponse::class, $response);
        $responseData = json_decode($response->getContent(), true);
        $this->assertFalse($responseData['success']);
    }

    /**
     * Test resendMultiple method resends multiple emails successfully
     */
    public function test_resend_multiple_resends_multiple_emails_successfully(): void
    {
        $ids = [1, 2, 3];
        
        $this->mockService
            ->shouldReceive('resend')
            ->once()
            ->with($ids)
            ->andReturn(['success' => true]);
        
        $controller = new EmailController(
            $this->mockService,
            $this->mockEmailCredentialService,
            $this->mockEmailTemplateService
        );
        
        $request = Request::create('/emails/resend-multiple', 'POST', ['ids' => '1,2,3']);
        
        $response = $controller->resendMultiple($request);
        
        $this->assertInstanceOf(\Illuminate\Http\JsonResponse::class, $response);
        $responseData = json_decode($response->getContent(), true);
        $this->assertTrue($responseData['success']);
    }

    /**
     * Test countAll method returns email count
     */
    public function test_count_all_returns_email_count(): void
    {
        $expectedCount = 150;
        
        $this->mockService
            ->shouldReceive('countAllEmails')
            ->once()
            ->andReturn($expectedCount);
        
        $controller = new EmailController(
            $this->mockService,
            $this->mockEmailCredentialService,
            $this->mockEmailTemplateService
        );
        
        $response = $controller->countAll();
        
        $this->assertInstanceOf(\Illuminate\Http\JsonResponse::class, $response);
        $responseData = json_decode($response->getContent(), true);
        $this->assertTrue($responseData['success']);
        $this->assertEquals($expectedCount, $responseData['data']['count']);
    }

    /**
     * Test middleware configuration
     */
    public function test_middleware_configuration(): void
    {
        $middleware = EmailController::middleware();
        
        $this->assertIsArray($middleware);
        $this->assertNotEmpty($middleware);
        
        // Check for specific middleware
        $middlewareNames = array_map(function($middleware) {
            return $middleware->middleware;
        }, $middleware);
        
        $this->assertContains('permission:read.email_logs', $middlewareNames);
        $this->assertContains('permission:send.emails', $middlewareNames);
        $this->assertContains('permission:resend.email_logs', $middlewareNames);
    }
}
