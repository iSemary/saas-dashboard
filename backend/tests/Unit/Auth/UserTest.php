<?php

namespace Tests\Unit\Auth;

use Tests\Unit\BaseEntityTest;
use Modules\Auth\Entities\User;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class UserTest extends BaseEntityTest
{
    protected string $modelClass = User::class;
    protected string $tableName = 'users';

    protected array $expectedFillable = [
        'customer_id',
        'name',
        'email',
        'username',
        'country_id',
        'language_id',
        'factor_authenticate',
        'google2fa_secret',
        'password',
    ];

    protected array $expectedHidden = [
        'password',
        'remember_token',
    ];

    protected array $expectedCasts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    protected array $expectedTraits = [
        HasApiTokens::class,
        HasFactory::class,
        Notifiable::class,
        HasRoles::class,
        SoftDeletes::class,
    ];

    protected array $expectedInterfaces = [
        Authenticatable::class,
    ];

    protected array $sampleData = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'username' => 'testuser',
        'password' => 'password123',
        'email_verified_at' => now(),
    ];

    /**
     * Test that the model has correct connection
     */
    public function test_has_correct_connection(): void
    {
        $model = new User();
        $this->assertEquals('landlord', $model->getConnectionName());
    }

    /**
     * Test that the model has correct guard name
     */
    public function test_has_correct_guard_name(): void
    {
        $model = new User();
        $this->assertEquals('web', $model->guard_name);
    }

    /**
     * Test that the model can be created with all required fields
     */
    public function test_can_be_created_with_all_required_fields(): void
    {
        $user = User::create($this->sampleData);
        
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($this->sampleData['name'], $user->name);
        $this->assertEquals($this->sampleData['email'], $user->email);
        $this->assertEquals($this->sampleData['username'], $user->username);
        $this->assertNotEquals($this->sampleData['password'], $user->password); // Should be hashed
    }

    /**
     * Test that the model can be updated
     */
    public function test_can_be_updated(): void
    {
        $user = User::create($this->sampleData);
        
        $updateData = [
            'name' => 'Updated User Name',
            'email' => 'updated@example.com',
        ];
        
        $user->update($updateData);
        
        $this->assertEquals($updateData['name'], $user->fresh()->name);
        $this->assertEquals($updateData['email'], $user->fresh()->email);
    }

    /**
     * Test that the model can be soft deleted
     */
    public function test_can_be_soft_deleted(): void
    {
        $user = User::create($this->sampleData);
        $userId = $user->id;
        
        $user->delete();
        
        $this->assertSoftDeleted('users', ['id' => $userId]);
        $this->assertNull(User::find($userId));
        $this->assertNotNull(User::withTrashed()->find($userId));
    }

    /**
     * Test that the model can be restored
     */
    public function test_can_be_restored(): void
    {
        $user = User::create($this->sampleData);
        $userId = $user->id;
        
        $user->delete();
        $user->restore();
        
        $this->assertDatabaseHas('users', [
            'id' => $userId,
            'deleted_at' => null
        ]);
        $this->assertNotNull(User::find($userId));
    }

    /**
     * Test that the model can be created using factory
     */
    public function test_can_be_created_using_factory(): void
    {
        $user = User::factory()->create();
        
        $this->assertInstanceOf(User::class, $user);
        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }

    /**
     * Test password hashing
     */
    public function test_password_is_hashed(): void
    {
        $user = User::create($this->sampleData);
        
        $this->assertNotEquals($this->sampleData['password'], $user->password);
        $this->assertTrue(password_verify($this->sampleData['password'], $user->password));
    }

    /**
     * Test email verification
     */
    public function test_email_verification(): void
    {
        $user = User::create($this->sampleData);
        
        $this->assertNotNull($user->email_verified_at);
        $this->assertTrue($user->hasVerifiedEmail());
    }

    /**
     * Test unverified email
     */
    public function test_unverified_email(): void
    {
        $userData = array_merge($this->sampleData, ['email_verified_at' => null]);
        $user = User::create($userData);
        
        $this->assertNull($user->email_verified_at);
        $this->assertFalse($user->hasVerifiedEmail());
    }

    /**
     * Test getCurrentTypeName method
     */
    public function test_get_current_type_name(): void
    {
        $user = new User();
        
        // Test with landlord connection
        config(['database.default' => 'landlord']);
        $this->assertEquals('landlord', $user->getCurrentTypeName());
        
        // Test with tenant connection
        config(['database.default' => 'tenant']);
        $this->assertEquals('tenant', $user->getCurrentTypeName());
    }

    /**
     * Test meta keys configuration
     */
    public function test_meta_keys_configuration(): void
    {
        $user = new User();
        $expectedMetaKeys = [
            'avatar',
            'gender',
            'address',
            'phone',
            'theme_mode',
            'timezone',
            'currency_id',
            'birthdate',
            'home_street_1',
            'home_street_2',
            'home_building_number',
            'home_landmark'
        ];
        
        $this->assertEquals($expectedMetaKeys, $user->metaKeys);
    }

    /**
     * Test file columns configuration
     */
    public function test_file_columns_configuration(): void
    {
        $user = new User();
        $expectedFileColumns = [
            'avatar' => [
                'folder' => 'avatar',
                'access_level' => 'public',
            ],
        ];
        
        $this->assertEquals($expectedFileColumns, $user->fileColumns);
    }

    /**
     * Test 2FA configuration
     */
    public function test_2fa_configuration(): void
    {
        $user = User::create(array_merge($this->sampleData, [
            'factor_authenticate' => true,
            'google2fa_secret' => 'test_secret_key'
        ]));
        
        $this->assertTrue($user->factor_authenticate);
        $this->assertEquals('test_secret_key', $user->google2fa_secret);
    }

    /**
     * Test user relationships
     */
    public function test_relationships(): void
    {
        $user = User::create($this->sampleData);
        
        // Test userMeta relationship
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $user->userMeta());
        
        // Test notifications relationship
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $user->notifications());
    }

    /**
     * Test role relationship
     */
    public function test_role_relationship(): void
    {
        $user = User::create($this->sampleData);
        
        // Test role method
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsToMany::class, $user->roles());
    }

    /**
     * Test API token functionality
     */
    public function test_api_token_functionality(): void
    {
        $user = User::create($this->sampleData);
        
        // Test that user can create API tokens
        $token = $user->createToken('Test Token');
        
        $this->assertInstanceOf(\Laravel\Passport\Token::class, $token->token);
        $this->assertIsString($token->accessToken);
    }

    /**
     * Test notification functionality
     */
    public function test_notification_functionality(): void
    {
        $user = User::create($this->sampleData);
        
        // Test that user can receive notifications
        $this->assertInstanceOf(\Illuminate\Notifications\Notification::class, 
            $user->notify(new \Illuminate\Notifications\Messages\MailMessage()));
    }

    /**
     * Test unique email constraint
     */
    public function test_unique_email_constraint(): void
    {
        User::create($this->sampleData);
        
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        User::create($this->sampleData);
    }

    /**
     * Test unique username constraint
     */
    public function test_unique_username_constraint(): void
    {
        User::create($this->sampleData);
        
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        User::create(array_merge($this->sampleData, [
            'email' => 'different@example.com'
        ]));
    }
}
