<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\Subscription\Entities\Plan;
use Modules\Subscription\Entities\PlanFeature;
use Modules\Subscription\Entities\PlanTrial;
use Modules\Utilities\Entities\Currency;
use Modules\Geography\Entities\Country;
use Modules\Customer\Entities\Brand;
use Modules\Subscription\Entities\PlanSubscription;

class CompleteUserJourneyTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupTestData();
    }

    protected function setupTestData()
    {
        // Create test currency
        Currency::create([
            'code' => 'USD',
            'name' => 'US Dollar',
            'symbol' => '$',
            'decimal_places' => 2,
            'is_active' => true,
            'conversion_rate' => 1.00,
        ]);

        // Create test country
        Country::create([
            'code' => 'US',
            'name' => 'United States',
            'status' => 'active',
        ]);

        // Create test plan
        $plan = Plan::create([
            'name' => 'Professional',
            'slug' => 'professional',
            'description' => 'Perfect for growing businesses',
            'sort_order' => 1,
            'is_popular' => true,
            'status' => 'active',
        ]);

        // Add plan features
        PlanFeature::create([
            'plan_id' => $plan->id,
            'feature_key' => 'users',
            'name' => 'Team Members',
            'feature_type' => 'numeric',
            'numeric_limit' => 25,
            'unit' => 'users',
            'sort_order' => 1,
            'status' => 'active',
        ]);

        // Add trial configuration
        PlanTrial::create([
            'plan_id' => $plan->id,
            'trial_days' => 14,
            'requires_payment_method' => false,
            'auto_convert' => true,
            'trial_type' => 'free',
            'status' => 'active',
        ]);
    }

    /** @test */
    public function complete_user_journey_from_registration_to_dashboard()
    {
        // Step 1: Simulate user registration (this would normally come from website)
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'SecurePass123!',
            'customer_title' => 'Acme Corporation',
            'customer_username' => 'acmecorp',
            'country_id' => 1,
            'category_id' => 1,
        ];

        // Simulate tenant and user creation (normally done by RegistrationService)
        $this->simulateTenantCreation($userData);

        // Step 2: Simulate email verification and login
        $user = \Modules\Auth\Entities\User::where('email', $userData['email'])->first();
        $this->actingAs($user);

        // Step 3: Test onboarding welcome page
        $response = $this->get('/onboarding');
        $response->assertStatus(200);
        $response->assertSee('Welcome, John Doe!');

        // Step 4: Test brand creation
        $brandData = [
            'name' => 'Acme SaaS Platform',
            'description' => 'Enterprise software solutions',
            'country_code' => 'US',
            'industry' => 'technology',
        ];

        $response = $this->post('/onboarding/create-brand', $brandData);
        $response->assertRedirect('/onboarding/select-plan');

        // Verify brand was created
        $this->assertDatabaseHas('brands', [
            'name' => 'Acme SaaS Platform',
            'tenant_id' => session('tenant_id'),
        ]);

        $brand = Brand::where('name', 'Acme SaaS Platform')->first();
        $this->assertNotNull($brand);

        // Step 5: Test plan selection
        $plan = Plan::where('slug', 'professional')->first();
        $currency = Currency::where('code', 'USD')->first();

        $planData = [
            'plan_id' => $plan->id,
            'currency_id' => $currency->id,
            'billing_cycle' => 'monthly',
        ];

        $response = $this->post('/onboarding/select-plan', $planData);
        $response->assertRedirect('/onboarding/select-modules');

        // Verify subscription was created
        $this->assertDatabaseHas('plan_subscriptions', [
            'brand_id' => $brand->id,
            'plan_id' => $plan->id,
            'status' => 'trial',
        ]);

        $subscription = PlanSubscription::where('brand_id', $brand->id)->first();
        $this->assertNotNull($subscription);
        $this->assertEquals('trial', $subscription->status);
        $this->assertNotNull($subscription->trial_ends_at);

        // Step 6: Test module selection
        $moduleData = [
            'modules' => ['hr', 'crm', 'surveys'],
        ];

        $response = $this->post('/onboarding/select-modules', $moduleData);
        $response->assertRedirect('/onboarding/complete');

        // Verify modules were saved
        $brand->refresh();
        $this->assertNotNull($brand->metadata);
        $this->assertArrayHasKey('selected_modules', $brand->metadata);
        $this->assertContains('hr', $brand->metadata['selected_modules']);
        $this->assertContains('crm', $brand->metadata['selected_modules']);
        $this->assertTrue($brand->metadata['onboarding_completed']);

        // Step 7: Test onboarding completion
        $response = $this->get('/onboarding/complete');
        $response->assertStatus(200);
        $response->assertSee('Congratulations!');

        // Step 8: Test redirect to dashboard
        $response = $this->post('/onboarding/complete');
        $response->assertRedirect('/');
        $response->assertSessionHas('success');

        // Step 9: Test dashboard access
        $response = $this->get('/');
        $response->assertStatus(200);

        // Verify dashboard shows correct information
        // (This would need to be implemented based on your dashboard structure)
        
        // Step 10: Verify onboarding middleware doesn't redirect anymore
        $response = $this->get('/');
        $response->assertStatus(200); // Should not redirect to onboarding
    }

    /** @test */
    public function incomplete_onboarding_redirects_to_onboarding()
    {
        // Create user without completing onboarding
        $user = \Modules\Auth\Entities\User::factory()->create();
        $this->actingAs($user);

        // Try to access dashboard
        $response = $this->get('/');
        
        // Should redirect to onboarding
        $response->assertRedirect('/onboarding');
    }

    /** @test */
    public function subscription_trial_management()
    {
        // Setup completed onboarding
        $this->completeOnboardingSetup();

        $subscription = PlanSubscription::first();
        
        // Test trial is active
        $this->assertTrue($subscription->isOnTrial());
        $this->assertFalse($subscription->isExpired());

        // Simulate trial expiration
        $subscription->update([
            'trial_ends_at' => now()->subDay(),
        ]);

        $subscription->refresh();
        $this->assertFalse($subscription->isOnTrial());
    }

    /** @test */
    public function brand_module_integration()
    {
        // Setup completed onboarding with specific modules
        $brand = $this->completeOnboardingSetup(['hr', 'crm']);

        // Verify modules are stored correctly
        $this->assertContains('hr', $brand->metadata['selected_modules']);
        $this->assertContains('crm', $brand->metadata['selected_modules']);
        $this->assertNotContains('surveys', $brand->metadata['selected_modules']);
    }

    /** @test */
    public function multi_tenant_isolation()
    {
        // Create first tenant/brand
        $brand1 = $this->completeOnboardingSetup(['hr'], 'tenant1');
        
        // Create second tenant/brand  
        $brand2 = $this->completeOnboardingSetup(['crm'], 'tenant2');

        // Verify brands are isolated
        $this->assertNotEquals($brand1->tenant_id, $brand2->tenant_id);
        
        // Verify subscriptions are isolated
        $subscription1 = PlanSubscription::where('brand_id', $brand1->id)->first();
        $subscription2 = PlanSubscription::where('brand_id', $brand2->id)->first();
        
        $this->assertNotEquals($subscription1->brand_id, $subscription2->brand_id);
    }

    protected function simulateTenantCreation($userData)
    {
        // Create tenant record (normally done by TenantRepository)
        $tenant = \Modules\Tenant\Entities\Tenant::create([
            'name' => $userData['customer_username'],
            'domain' => $userData['customer_username'] . '.app.com',
            'database' => 'test_' . $userData['customer_username'],
        ]);

        // Create customer record
        \Modules\Customer\Entities\Customer::create([
            'name' => $userData['customer_title'],
            'username' => $userData['customer_username'],
            'category_id' => $userData['category_id'],
            'tenant_id' => $tenant->id,
        ]);

        // Create user
        $user = \Modules\Auth\Entities\User::create([
            'name' => $userData['name'],
            'email' => $userData['email'],
            'password' => bcrypt($userData['password']),
            'country_id' => $userData['country_id'],
            'email_verified_at' => now(),
        ]);

        // Set session data
        session(['tenant_id' => $tenant->id]);

        return $user;
    }

    protected function completeOnboardingSetup($modules = ['hr', 'crm'], $tenantName = 'testcorp')
    {
        // Create user and tenant
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'customer_title' => 'Test Corp',
            'customer_username' => $tenantName,
            'country_id' => 1,
            'category_id' => 1,
        ];

        $user = $this->simulateTenantCreation($userData);
        $this->actingAs($user);

        // Create brand
        $brand = Brand::create([
            'name' => 'Test Brand',
            'slug' => 'test-brand',
            'tenant_id' => session('tenant_id'),
            'metadata' => [
                'selected_modules' => $modules,
                'onboarding_completed' => true,
                'onboarding_completed_at' => now(),
            ],
        ]);

        // Create subscription
        $plan = Plan::first();
        $currency = Currency::first();

        PlanSubscription::create([
            'subscription_id' => 'sub_test_' . uniqid(),
            'brand_id' => $brand->id,
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'currency_id' => $currency->id,
            'price' => 0,
            'billing_cycle' => 'monthly',
            'trial_starts_at' => now(),
            'trial_ends_at' => now()->addDays(14),
            'status' => 'trial',
            'subscription_data' => [
                'selected_modules' => $modules,
                'onboarding_completed' => true,
            ],
        ]);

        return $brand;
    }
}
