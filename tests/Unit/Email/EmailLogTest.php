<?php

namespace Tests\Unit\Email;

use Tests\Unit\BaseEntityTest;
use Modules\Email\Entities\EmailLog;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;

class EmailLogTest extends BaseEntityTest
{
    protected string $modelClass = EmailLog::class;
    protected string $tableName = 'email_logs';

    protected array $expectedFillable = [
        'email_recipient_id',
        'email_template_log_id',
        'email_campaign_id',
        'email_credential_id',
        'email',
        'status',
        'error_message',
        'subject',
        'body',
        'email_recipient_meta',
        'opened_at',
        'clicked_at',
    ];

    protected array $expectedTraits = [
        HasFactory::class,
        SoftDeletes::class,
        \OwenIt\Auditing\Auditable::class,
    ];

    protected array $expectedInterfaces = [
        Auditable::class,
    ];

    protected array $sampleData = [
        'email' => 'test@example.com',
        'status' => 'sent',
        'subject' => 'Test Email Subject',
        'body' => 'Test email body content',
        'email_credential_id' => 1,
    ];

    /**
     * Test that the model has correct connection
     */
    public function test_has_correct_connection(): void
    {
        $model = new EmailLog();
        $this->assertEquals('landlord', $model->getConnectionName());
    }

    /**
     * Test that the model can be created with all required fields
     */
    public function test_can_be_created_with_all_required_fields(): void
    {
        $emailLog = EmailLog::create($this->sampleData);
        
        $this->assertInstanceOf(EmailLog::class, $emailLog);
        $this->assertEquals($this->sampleData['email'], $emailLog->email);
        $this->assertEquals($this->sampleData['status'], $emailLog->status);
        $this->assertEquals($this->sampleData['subject'], $emailLog->subject);
        $this->assertEquals($this->sampleData['body'], $emailLog->body);
    }

    /**
     * Test that the model can be updated
     */
    public function test_can_be_updated(): void
    {
        $emailLog = EmailLog::create($this->sampleData);
        
        $updateData = [
            'status' => 'failed',
            'error_message' => 'SMTP connection failed',
            'opened_at' => now(),
        ];
        
        $emailLog->update($updateData);
        
        $this->assertEquals($updateData['status'], $emailLog->fresh()->status);
        $this->assertEquals($updateData['error_message'], $emailLog->fresh()->error_message);
        $this->assertNotNull($emailLog->fresh()->opened_at);
    }

    /**
     * Test that the model can be soft deleted
     */
    public function test_can_be_soft_deleted(): void
    {
        $emailLog = EmailLog::create($this->sampleData);
        $emailLogId = $emailLog->id;
        
        $emailLog->delete();
        
        $this->assertSoftDeleted('email_logs', ['id' => $emailLogId]);
        $this->assertNull(EmailLog::find($emailLogId));
        $this->assertNotNull(EmailLog::withTrashed()->find($emailLogId));
    }

    /**
     * Test that the model can be restored
     */
    public function test_can_be_restored(): void
    {
        $emailLog = EmailLog::create($this->sampleData);
        $emailLogId = $emailLog->id;
        
        $emailLog->delete();
        $emailLog->restore();
        
        $this->assertDatabaseHas('email_logs', [
            'id' => $emailLogId,
            'deleted_at' => null
        ]);
        $this->assertNotNull(EmailLog::find($emailLogId));
    }

    /**
     * Test that the model can be created using factory
     */
    public function test_can_be_created_using_factory(): void
    {
        $emailLog = EmailLog::factory()->create();
        
        $this->assertInstanceOf(EmailLog::class, $emailLog);
        $this->assertDatabaseHas('email_logs', ['id' => $emailLog->id]);
    }

    /**
     * Test that the model has auditing enabled
     */
    public function test_has_auditing_enabled(): void
    {
        $emailLog = EmailLog::create($this->sampleData);
        
        // Update the model to trigger auditing
        $emailLog->update(['status' => 'opened']);
        
        // Check if audit record was created
        $this->assertDatabaseHas('audits', [
            'auditable_type' => EmailLog::class,
            'auditable_id' => $emailLog->id,
            'event' => 'updated'
        ]);
    }

    /**
     * Test status enum values
     */
    public function test_status_enum_values(): void
    {
        $sentLog = EmailLog::create(array_merge($this->sampleData, ['status' => 'sent']));
        $failedLog = EmailLog::create(array_merge($this->sampleData, ['status' => 'failed']));
        $pendingLog = EmailLog::create(array_merge($this->sampleData, ['status' => 'pending']));
        $openedLog = EmailLog::create(array_merge($this->sampleData, ['status' => 'opened']));
        
        $this->assertEquals('sent', $sentLog->status);
        $this->assertEquals('failed', $failedLog->status);
        $this->assertEquals('pending', $pendingLog->status);
        $this->assertEquals('opened', $openedLog->status);
    }

    /**
     * Test email tracking timestamps
     */
    public function test_email_tracking_timestamps(): void
    {
        $emailLog = EmailLog::create($this->sampleData);
        
        $openedAt = now();
        $clickedAt = now()->addMinutes(5);
        
        $emailLog->update([
            'opened_at' => $openedAt,
            'clicked_at' => $clickedAt,
        ]);
        
        $this->assertEquals($openedAt->format('Y-m-d H:i:s'), $emailLog->fresh()->opened_at->format('Y-m-d H:i:s'));
        $this->assertEquals($clickedAt->format('Y-m-d H:i:s'), $emailLog->fresh()->clicked_at->format('Y-m-d H:i:s'));
    }

    /**
     * Test error message storage
     */
    public function test_error_message_storage(): void
    {
        $errorMessage = 'SMTP connection timeout after 30 seconds';
        
        $emailLog = EmailLog::create(array_merge($this->sampleData, [
            'status' => 'failed',
            'error_message' => $errorMessage
        ]));
        
        $this->assertEquals('failed', $emailLog->status);
        $this->assertEquals($errorMessage, $emailLog->error_message);
    }

    /**
     * Test email recipient meta storage
     */
    public function test_email_recipient_meta_storage(): void
    {
        $meta = [
            'user_id' => 123,
            'subscription_type' => 'premium',
            'preferences' => ['newsletter' => true, 'promotions' => false]
        ];
        
        $emailLog = EmailLog::create(array_merge($this->sampleData, [
            'email_recipient_meta' => json_encode($meta)
        ]));
        
        $this->assertEquals(json_encode($meta), $emailLog->email_recipient_meta);
    }

    /**
     * Test HTML body storage
     */
    public function test_html_body_storage(): void
    {
        $htmlBody = '<html><body><h1>Welcome</h1><p>This is a test email with <strong>HTML</strong> content.</p></body></html>';
        
        $emailLog = EmailLog::create(array_merge($this->sampleData, [
            'body' => $htmlBody
        ]));
        
        $this->assertEquals($htmlBody, $emailLog->body);
    }

    /**
     * Test long subject line storage
     */
    public function test_long_subject_line_storage(): void
    {
        $longSubject = str_repeat('This is a very long email subject line. ', 10);
        
        $emailLog = EmailLog::create(array_merge($this->sampleData, [
            'subject' => $longSubject
        ]));
        
        $this->assertEquals($longSubject, $emailLog->subject);
    }
}
