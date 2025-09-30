<?php

namespace Tests\Unit\Email;

use Tests\Unit\BaseEntityTest;
use Modules\Email\Entities\EmailCredential;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;

class EmailCredentialTest extends BaseEntityTest
{
    protected string $modelClass = EmailCredential::class;
    protected string $tableName = 'email_credentials';

    protected array $expectedFillable = [
        'name',
        'description',
        'from_address',
        'from_name',
        'mailer',
        'host',
        'port',
        'username',
        'password',
        'encryption',
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
        'name' => 'Test SMTP',
        'description' => 'Test SMTP configuration',
        'from_address' => 'test@example.com',
        'from_name' => 'Test Sender',
        'mailer' => 'smtp',
        'host' => 'smtp.example.com',
        'port' => 587,
        'username' => 'test@example.com',
        'password' => 'testpassword',
        'encryption' => 'tls',
        'status' => 'active',
    ];

    /**
     * Test that the model has correct connection
     */
    public function test_has_correct_connection(): void
    {
        $model = new EmailCredential();
        $this->assertEquals('landlord', $model->getConnectionName());
    }

    /**
     * Test that the model has correct titles
     */
    public function test_has_correct_titles(): void
    {
        $model = new EmailCredential();
        $this->assertEquals('email_credential', $model->singleTitle);
        $this->assertEquals('email_credentials', $model->pluralTitle);
    }

    /**
     * Test that the model can be created with all required fields
     */
    public function test_can_be_created_with_all_required_fields(): void
    {
        $emailCredential = EmailCredential::create($this->sampleData);
        
        $this->assertInstanceOf(EmailCredential::class, $emailCredential);
        $this->assertEquals($this->sampleData['name'], $emailCredential->name);
        $this->assertEquals($this->sampleData['from_address'], $emailCredential->from_address);
        $this->assertEquals($this->sampleData['host'], $emailCredential->host);
        $this->assertEquals($this->sampleData['port'], $emailCredential->port);
    }

    /**
     * Test that the model can be updated
     */
    public function test_can_be_updated(): void
    {
        $emailCredential = EmailCredential::create($this->sampleData);
        
        $updateData = [
            'name' => 'Updated SMTP',
            'host' => 'updated.smtp.com',
            'port' => 465,
            'status' => 'inactive',
        ];
        
        $emailCredential->update($updateData);
        
        $this->assertEquals($updateData['name'], $emailCredential->fresh()->name);
        $this->assertEquals($updateData['host'], $emailCredential->fresh()->host);
        $this->assertEquals($updateData['port'], $emailCredential->fresh()->port);
        $this->assertEquals($updateData['status'], $emailCredential->fresh()->status);
    }

    /**
     * Test that the model can be soft deleted
     */
    public function test_can_be_soft_deleted(): void
    {
        $emailCredential = EmailCredential::create($this->sampleData);
        $emailCredentialId = $emailCredential->id;
        
        $emailCredential->delete();
        
        $this->assertSoftDeleted('email_credentials', ['id' => $emailCredentialId]);
        $this->assertNull(EmailCredential::find($emailCredentialId));
        $this->assertNotNull(EmailCredential::withTrashed()->find($emailCredentialId));
    }

    /**
     * Test that the model can be restored
     */
    public function test_can_be_restored(): void
    {
        $emailCredential = EmailCredential::create($this->sampleData);
        $emailCredentialId = $emailCredential->id;
        
        $emailCredential->delete();
        $emailCredential->restore();
        
        $this->assertDatabaseHas('email_credentials', [
            'id' => $emailCredentialId,
            'deleted_at' => null
        ]);
        $this->assertNotNull(EmailCredential::find($emailCredentialId));
    }

    /**
     * Test that the model can be created using factory
     */
    public function test_can_be_created_using_factory(): void
    {
        $emailCredential = EmailCredential::factory()->create();
        
        $this->assertInstanceOf(EmailCredential::class, $emailCredential);
        $this->assertDatabaseHas('email_credentials', ['id' => $emailCredential->id]);
    }

    /**
     * Test that the model has auditing enabled
     */
    public function test_has_auditing_enabled(): void
    {
        $emailCredential = EmailCredential::create($this->sampleData);
        
        // Update the model to trigger auditing
        $emailCredential->update(['name' => 'Updated Name']);
        
        // Check if audit record was created
        $this->assertDatabaseHas('audits', [
            'auditable_type' => EmailCredential::class,
            'auditable_id' => $emailCredential->id,
            'event' => 'updated'
        ]);
    }

    /**
     * Test status enum values
     */
    public function test_status_enum_values(): void
    {
        $activeCredential = EmailCredential::create(array_merge($this->sampleData, ['status' => 'active']));
        $inactiveCredential = EmailCredential::create(array_merge($this->sampleData, ['status' => 'inactive']));
        
        $this->assertEquals('active', $activeCredential->status);
        $this->assertEquals('inactive', $inactiveCredential->status);
    }

    /**
     * Test mailer types
     */
    public function test_mailer_types(): void
    {
        $smtpCredential = EmailCredential::create(array_merge($this->sampleData, ['mailer' => 'smtp']));
        $sendmailCredential = EmailCredential::create(array_merge($this->sampleData, ['mailer' => 'sendmail']));
        
        $this->assertEquals('smtp', $smtpCredential->mailer);
        $this->assertEquals('sendmail', $sendmailCredential->mailer);
    }

    /**
     * Test encryption types
     */
    public function test_encryption_types(): void
    {
        $tlsCredential = EmailCredential::create(array_merge($this->sampleData, ['encryption' => 'tls']));
        $sslCredential = EmailCredential::create(array_merge($this->sampleData, ['encryption' => 'ssl']));
        $nullCredential = EmailCredential::create(array_merge($this->sampleData, ['encryption' => null]));
        
        $this->assertEquals('tls', $tlsCredential->encryption);
        $this->assertEquals('ssl', $sslCredential->encryption);
        $this->assertNull($nullCredential->encryption);
    }

    /**
     * Test port validation
     */
    public function test_port_validation(): void
    {
        $credential587 = EmailCredential::create(array_merge($this->sampleData, ['port' => 587]));
        $credential465 = EmailCredential::create(array_merge($this->sampleData, ['port' => 465]));
        $credential25 = EmailCredential::create(array_merge($this->sampleData, ['port' => 25]));
        
        $this->assertEquals(587, $credential587->port);
        $this->assertEquals(465, $credential465->port);
        $this->assertEquals(25, $credential25->port);
    }

    /**
     * Test email address validation
     */
    public function test_email_address_validation(): void
    {
        $validEmail = 'test@example.com';
        $credential = EmailCredential::create(array_merge($this->sampleData, [
            'from_address' => $validEmail,
            'username' => $validEmail
        ]));
        
        $this->assertEquals($validEmail, $credential->from_address);
        $this->assertEquals($validEmail, $credential->username);
    }

    /**
     * Test password storage
     */
    public function test_password_storage(): void
    {
        $password = 'secretpassword123';
        $credential = EmailCredential::create(array_merge($this->sampleData, [
            'password' => $password
        ]));
        
        $this->assertEquals($password, $credential->password);
    }
}
