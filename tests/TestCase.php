<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Set up test environment
        $this->setUpTestDatabase();
    }

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }

    protected function setUpTestDatabase(): void
    {
        // Set connection to landlord for testing
        config(['database.default' => 'landlord']);
        
        // Run migrations for test database
        Artisan::call('migrate:fresh', [
            '--env' => 'testing',
            '--force' => true
        ]);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Create a test user with default attributes
     */
    protected function createTestUser(array $attributes = []): \Modules\Auth\Entities\User
    {
        $defaultAttributes = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'username' => $this->faker->unique()->userName,
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ];

        return \Modules\Auth\Entities\User::create(array_merge($defaultAttributes, $attributes));
    }

    /**
     * Create a test tenant with default attributes
     */
    protected function createTestTenant(array $attributes = []): \Modules\Tenant\Entities\Tenant
    {
        $defaultAttributes = [
            'name' => $this->faker->company,
            'domain' => $this->faker->unique()->domainName,
            'database' => 'tenant_' . $this->faker->unique()->randomNumber(6),
            'status' => 'active',
        ];

        return \Modules\Tenant\Entities\Tenant::create(array_merge($defaultAttributes, $attributes));
    }

    /**
     * Assert that a model has the expected fillable attributes
     */
    protected function assertModelHasFillable($model, array $expectedFillable): void
    {
        $this->assertEquals($expectedFillable, $model->getFillable());
    }

    /**
     * Assert that a model has the expected hidden attributes
     */
    protected function assertModelHasHidden($model, array $expectedHidden): void
    {
        $this->assertEquals($expectedHidden, $model->getHidden());
    }

    /**
     * Assert that a model has the expected casts
     */
    protected function assertModelHasCasts($model, array $expectedCasts): void
    {
        $this->assertEquals($expectedCasts, $model->getCasts());
    }

    /**
     * Assert that a model uses the expected traits
     */
    protected function assertModelUsesTraits($model, array $expectedTraits): void
    {
        $usedTraits = class_uses_recursive(get_class($model));
        
        foreach ($expectedTraits as $trait) {
            $this->assertContains($trait, $usedTraits, 
                "Model " . get_class($model) . " should use trait {$trait}");
        }
    }

    /**
     * Assert that a model implements the expected interfaces
     */
    protected function assertModelImplementsInterfaces($model, array $expectedInterfaces): void
    {
        $implementedInterfaces = class_implements(get_class($model));
        
        foreach ($expectedInterfaces as $interface) {
            $this->assertContains($interface, $implementedInterfaces,
                "Model " . get_class($model) . " should implement interface {$interface}");
        }
    }
}
