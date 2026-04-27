<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use Modules\Email\Services\EmailService;
use Modules\Email\Repositories\EmailRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;

class EmailServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $mockRepository;
    protected $emailService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockRepository = Mockery::mock(EmailRepository::class);
        $this->emailService = new EmailService($this->mockRepository);
    }

    /**
     * Test that the service can be instantiated
     */
    public function test_can_be_instantiated(): void
    {
        $this->assertInstanceOf(EmailService::class, $this->emailService);
    }

    /**
     * Test send method
     */
    public function test_send(): void
    {
        $data = [
            'email' => 'test@example.com',
            'subject' => 'Test Subject',
            'body' => 'Test Body',
            'email_credential_id' => 1,
        ];

        $expectedResult = ['success' => true];

        $this->mockRepository
            ->shouldReceive('send')
            ->once()
            ->with($data)
            ->andReturn($expectedResult);

        $result = $this->emailService->send($data);

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Test getById method
     */
    public function test_get_by_id(): void
    {
        $id = 1;
        $expectedResult = (object)['id' => 1, 'email' => 'test@example.com'];

        $this->mockRepository
            ->shouldReceive('getById')
            ->once()
            ->with($id)
            ->andReturn($expectedResult);

        $result = $this->emailService->getById($id);

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Test resend method
     */
    public function test_resend(): void
    {
        $ids = [1, 2, 3];
        $expectedResult = ['success' => true];

        $this->mockRepository
            ->shouldReceive('resend')
            ->once()
            ->with($ids)
            ->andReturn($expectedResult);

        $result = $this->emailService->resend($ids);

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Test countAllEmails method
     */
    public function test_count_all_emails(): void
    {
        $expectedCount = 150;

        $this->mockRepository
            ->shouldReceive('countAllEmails')
            ->once()
            ->andReturn($expectedCount);

        $result = $this->emailService->countAllEmails();

        $this->assertEquals($expectedCount, $result);
    }

    /**
     * Test send method with invalid data
     */
    public function test_send_with_invalid_data(): void
    {
        $data = [
            'email' => 'invalid-email',
            'subject' => '',
            'body' => '',
        ];

        $expectedResult = ['success' => false, 'message' => 'Invalid email data'];

        $this->mockRepository
            ->shouldReceive('send')
            ->once()
            ->with($data)
            ->andReturn($expectedResult);

        $result = $this->emailService->send($data);

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Test getById method with non-existent ID
     */
    public function test_get_by_id_with_non_existent_id(): void
    {
        $id = 999;

        $this->mockRepository
            ->shouldReceive('getById')
            ->once()
            ->with($id)
            ->andReturn(null);

        $result = $this->emailService->getById($id);

        $this->assertNull($result);
    }

    /**
     * Test resend method with empty array
     */
    public function test_resend_with_empty_array(): void
    {
        $ids = [];
        $expectedResult = ['success' => false, 'message' => 'No emails to resend'];

        $this->mockRepository
            ->shouldReceive('resend')
            ->once()
            ->with($ids)
            ->andReturn($expectedResult);

        $result = $this->emailService->resend($ids);

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Test countAllEmails method returns zero
     */
    public function test_count_all_emails_returns_zero(): void
    {
        $expectedCount = 0;

        $this->mockRepository
            ->shouldReceive('countAllEmails')
            ->once()
            ->andReturn($expectedCount);

        $result = $this->emailService->countAllEmails();

        $this->assertEquals($expectedCount, $result);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
