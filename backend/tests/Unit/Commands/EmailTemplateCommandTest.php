<?php

namespace Tests\Unit\Commands;

use Tests\TestCase;
use App\Console\Commands\EmailTemplateCommand;
use Modules\Email\Services\EmailTemplateService;
use Modules\Email\Entities\EmailTemplate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Mockery;

class EmailTemplateCommandTest extends TestCase
{
    use RefreshDatabase;

    protected $mockService;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->mockService = Mockery::mock(EmailTemplateService::class);
        $this->app->instance(EmailTemplateService::class, $this->mockService);
    }

    /**
     * Test that the command can be instantiated
     */
    public function test_can_be_instantiated(): void
    {
        $command = new EmailTemplateCommand($this->mockService);
        $this->assertInstanceOf(EmailTemplateCommand::class, $command);
    }

    /**
     * Test command signature
     */
    public function test_command_signature(): void
    {
        $command = new EmailTemplateCommand($this->mockService);
        $this->assertStringContainsString('email:templates', $command->getSignature());
    }

    /**
     * Test command description
     */
    public function test_command_description(): void
    {
        $command = new EmailTemplateCommand($this->mockService);
        $this->assertEquals('Manage email templates dynamically', $command->getDescription());
    }

    /**
     * Test list action
     */
    public function test_list_action(): void
    {
        $templates = collect([
            (object)['id' => 1, 'name' => 'Test Template', 'status' => 'active'],
            (object)['id' => 2, 'name' => 'Test Template 2', 'status' => 'inactive'],
        ]);
        
        $this->mockService
            ->shouldReceive('getAll')
            ->once()
            ->andReturn($templates);
        
        $this->artisan('email:templates', ['action' => 'list'])
            ->assertExitCode(0);
    }

    /**
     * Test create action
     */
    public function test_create_action(): void
    {
        $data = [
            'name' => 'Test Template',
            'subject' => 'Test Subject',
            'description' => 'Test Description',
            'body' => 'Test Body',
            'status' => 'active',
        ];
        
        $this->mockService
            ->shouldReceive('create')
            ->once()
            ->with($data)
            ->andReturn(true);
        
        $this->artisan('email:templates', [
            'action' => 'create',
            '--name' => 'Test Template',
            '--subject' => 'Test Subject',
            '--description' => 'Test Description',
            '--body' => 'Test Body',
            '--status' => 'active',
        ])->assertExitCode(0);
    }

    /**
     * Test update action
     */
    public function test_update_action(): void
    {
        $id = 1;
        $data = [
            'name' => 'Updated Template',
            'subject' => 'Updated Subject',
        ];
        
        $this->mockService
            ->shouldReceive('update')
            ->once()
            ->with($id, $data)
            ->andReturn(true);
        
        $this->artisan('email:templates', [
            'action' => 'update',
            '--id' => $id,
            '--name' => 'Updated Template',
            '--subject' => 'Updated Subject',
        ])->assertExitCode(0);
    }

    /**
     * Test delete action
     */
    public function test_delete_action(): void
    {
        $id = 1;
        
        $this->mockService
            ->shouldReceive('delete')
            ->once()
            ->with($id)
            ->andReturn(true);
        
        $this->artisan('email:templates', [
            'action' => 'delete',
            '--id' => $id,
        ])->assertExitCode(0);
    }

    /**
     * Test seed action
     */
    public function test_seed_action(): void
    {
        $this->mockService
            ->shouldReceive('getAll')
            ->once()
            ->andReturn(collect([]));
        
        $this->mockService
            ->shouldReceive('create')
            ->times(3)
            ->andReturn(true);
        
        $this->artisan('email:templates', ['action' => 'seed'])
            ->assertExitCode(0);
    }

    /**
     * Test invalid action
     */
    public function test_invalid_action(): void
    {
        $this->artisan('email:templates', ['action' => 'invalid'])
            ->assertExitCode(1);
    }

    /**
     * Test create action with missing required fields
     */
    public function test_create_action_with_missing_required_fields(): void
    {
        $this->artisan('email:templates', [
            'action' => 'create',
            '--name' => 'Test Template',
            // Missing required fields
        ])->assertExitCode(1);
    }

    /**
     * Test update action without ID
     */
    public function test_update_action_without_id(): void
    {
        $this->artisan('email:templates', [
            'action' => 'update',
            '--name' => 'Updated Template',
        ])->assertExitCode(1);
    }

    /**
     * Test delete action without ID
     */
    public function test_delete_action_without_id(): void
    {
        $this->artisan('email:templates', [
            'action' => 'delete',
        ])->assertExitCode(1);
    }

    /**
     * Test create action with service error
     */
    public function test_create_action_with_service_error(): void
    {
        $this->mockService
            ->shouldReceive('create')
            ->once()
            ->andThrow(new \Exception('Service error'));
        
        $this->artisan('email:templates', [
            'action' => 'create',
            '--name' => 'Test Template',
            '--subject' => 'Test Subject',
            '--description' => 'Test Description',
            '--body' => 'Test Body',
        ])->assertExitCode(1);
    }

    /**
     * Test update action with service error
     */
    public function test_update_action_with_service_error(): void
    {
        $this->mockService
            ->shouldReceive('update')
            ->once()
            ->andThrow(new \Exception('Service error'));
        
        $this->artisan('email:templates', [
            'action' => 'update',
            '--id' => 1,
            '--name' => 'Updated Template',
        ])->assertExitCode(1);
    }

    /**
     * Test delete action with service error
     */
    public function test_delete_action_with_service_error(): void
    {
        $this->mockService
            ->shouldReceive('delete')
            ->once()
            ->andThrow(new \Exception('Service error'));
        
        $this->artisan('email:templates', [
            'action' => 'delete',
            '--id' => 1,
        ])->assertExitCode(1);
    }

    /**
     * Test seed action with existing templates
     */
    public function test_seed_action_with_existing_templates(): void
    {
        $existingTemplates = collect([
            (object)['id' => 1, 'name' => 'Existing Template'],
        ]);
        
        $this->mockService
            ->shouldReceive('getAll')
            ->once()
            ->andReturn($existingTemplates);
        
        $this->artisan('email:templates', ['action' => 'seed'])
            ->assertExitCode(0);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
