<?php

namespace Tests\Unit\Email;

use Tests\Unit\BaseEntityTest;
use Modules\Email\Entities\EmailTemplate;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;

class EmailTemplateTest extends BaseEntityTest
{
    protected string $modelClass = EmailTemplate::class;
    protected string $tableName = 'email_templates';

    protected array $expectedFillable = [
        'name',
        'description',
        'subject',
        'body',
        'status',
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
        'name' => 'Test Email Template',
        'description' => 'Test description',
        'subject' => 'Test Subject',
        'body' => '<h1>Test Body</h1>',
        'status' => 'active',
    ];

    /**
     * Test that the model has correct connection
     */
    public function test_has_correct_connection(): void
    {
        $model = new EmailTemplate();
        $this->assertEquals('landlord', $model->getConnectionName());
    }

    /**
     * Test that the model has correct titles
     */
    public function test_has_correct_titles(): void
    {
        $model = new EmailTemplate();
        $this->assertEquals('email_template', $model->singleTitle);
        $this->assertEquals('email_templates', $model->pluralTitle);
    }

    /**
     * Test that the model can be created with all required fields
     */
    public function test_can_be_created_with_all_required_fields(): void
    {
        $emailTemplate = EmailTemplate::create($this->sampleData);
        
        $this->assertInstanceOf(EmailTemplate::class, $emailTemplate);
        $this->assertEquals($this->sampleData['name'], $emailTemplate->name);
        $this->assertEquals($this->sampleData['description'], $emailTemplate->description);
        $this->assertEquals($this->sampleData['subject'], $emailTemplate->subject);
        $this->assertEquals($this->sampleData['body'], $emailTemplate->body);
        $this->assertEquals($this->sampleData['status'], $emailTemplate->status);
    }

    /**
     * Test that the model validates required fields
     */
    public function test_validates_required_fields(): void
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        EmailTemplate::create([
            'name' => 'Test Template',
            // Missing required fields
        ]);
    }

    /**
     * Test that the model can be updated
     */
    public function test_can_be_updated(): void
    {
        $emailTemplate = EmailTemplate::create($this->sampleData);
        
        $updateData = [
            'name' => 'Updated Template Name',
            'subject' => 'Updated Subject',
            'status' => 'inactive',
        ];
        
        $emailTemplate->update($updateData);
        
        $this->assertEquals($updateData['name'], $emailTemplate->fresh()->name);
        $this->assertEquals($updateData['subject'], $emailTemplate->fresh()->subject);
        $this->assertEquals($updateData['status'], $emailTemplate->fresh()->status);
    }

    /**
     * Test that the model can be soft deleted
     */
    public function test_can_be_soft_deleted(): void
    {
        $emailTemplate = EmailTemplate::create($this->sampleData);
        $emailTemplateId = $emailTemplate->id;
        
        $emailTemplate->delete();
        
        $this->assertSoftDeleted('email_templates', ['id' => $emailTemplateId]);
        $this->assertNull(EmailTemplate::find($emailTemplateId));
        $this->assertNotNull(EmailTemplate::withTrashed()->find($emailTemplateId));
    }

    /**
     * Test that the model can be restored
     */
    public function test_can_be_restored(): void
    {
        $emailTemplate = EmailTemplate::create($this->sampleData);
        $emailTemplateId = $emailTemplate->id;
        
        $emailTemplate->delete();
        $emailTemplate->restore();
        
        $this->assertDatabaseHas('email_templates', [
            'id' => $emailTemplateId,
            'deleted_at' => null
        ]);
        $this->assertNotNull(EmailTemplate::find($emailTemplateId));
    }

    /**
     * Test that the model can be created using factory
     */
    public function test_can_be_created_using_factory(): void
    {
        $emailTemplate = EmailTemplate::factory()->create();
        
        $this->assertInstanceOf(EmailTemplate::class, $emailTemplate);
        $this->assertDatabaseHas('email_templates', ['id' => $emailTemplate->id]);
    }

    /**
     * Test that the model has auditing enabled
     */
    public function test_has_auditing_enabled(): void
    {
        $emailTemplate = EmailTemplate::create($this->sampleData);
        
        // Update the model to trigger auditing
        $emailTemplate->update(['name' => 'Updated Name']);
        
        // Check if audit record was created
        $this->assertDatabaseHas('audits', [
            'auditable_type' => EmailTemplate::class,
            'auditable_id' => $emailTemplate->id,
            'event' => 'updated'
        ]);
    }

    /**
     * Test status enum values
     */
    public function test_status_enum_values(): void
    {
        $activeTemplate = EmailTemplate::create(array_merge($this->sampleData, ['status' => 'active']));
        $inactiveTemplate = EmailTemplate::create(array_merge($this->sampleData, ['status' => 'inactive']));
        
        $this->assertEquals('active', $activeTemplate->status);
        $this->assertEquals('inactive', $inactiveTemplate->status);
    }

    /**
     * Test that HTML content is properly stored
     */
    public function test_html_content_is_properly_stored(): void
    {
        $htmlBody = '<html><body><h1>Welcome</h1><p>This is a test email.</p></body></html>';
        
        $emailTemplate = EmailTemplate::create(array_merge($this->sampleData, [
            'body' => $htmlBody
        ]));
        
        $this->assertEquals($htmlBody, $emailTemplate->body);
    }

    /**
     * Test that long content can be stored
     */
    public function test_long_content_can_be_stored(): void
    {
        $longBody = str_repeat('This is a long email body content. ', 100);
        
        $emailTemplate = EmailTemplate::create(array_merge($this->sampleData, [
            'body' => $longBody
        ]));
        
        $this->assertEquals($longBody, $emailTemplate->body);
    }
}
