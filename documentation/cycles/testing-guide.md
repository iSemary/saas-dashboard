# Complete Testing Guide: Registration to Dashboard

## Overview

This guide provides comprehensive testing scenarios for the complete user journey from registration to dashboard access, including all integrations between tenant, brand, subscription, payment, and module systems.

## Pre-Testing Setup

### 1. Database Preparation
```bash
# Run migrations
php artisan migrate --database=landlord
php artisan migrate --database=tenant

# Seed test data
php artisan db:seed --class=PlansSeeder
php artisan db:seed --class=CurrencySeeder
php artisan db:seed --class=CountrySeeder
```

### 2. Environment Configuration
```env
# .env settings for testing
APP_ENV=testing
DB_CONNECTION=mysql
LANDLORD_DB_CONNECTION=landlord
TENANT_DB_CONNECTION=tenant

# Email testing
MAIL_MAILER=log
QUEUE_CONNECTION=sync

# Payment testing
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
```

### 3. Test Data Requirements
- At least 3 plans (Starter, Professional, Enterprise)
- Multiple currencies (USD, EUR, GBP)
- Country data with active status
- Email templates configured

## Testing Scenarios

### Scenario 1: Happy Path - Complete Registration Flow

#### Test Case 1.1: New User Registration
**Objective**: Test complete registration from website to dashboard

**Steps**:
1. **Navigate to Registration**
   ```
   URL: https://website.com/register
   Expected: Registration form displayed
   ```

2. **Fill Registration Form**
   ```
   Data:
   - Name: "John Doe"
   - Email: "john@example.com"
   - Password: "SecurePass123!"
   - Company: "Acme Corp"
   - Username: "acmecorp"
   - Country: "United States"
   - Category: "Technology"
   
   Expected: Form validation passes
   ```

3. **Submit Registration**
   ```
   Action: Click "Register" button
   Expected: 
   - Success message displayed
   - Tenant created in landlord DB
   - User created in tenant DB
   - Email verification sent
   - Redirect URL provided
   ```

4. **Verify Database Changes**
   ```sql
   -- Landlord DB
   SELECT * FROM tenants WHERE name = 'acmecorp';
   SELECT * FROM customers WHERE username = 'acmecorp';
   
   -- Tenant DB (acmecorp_db)
   SELECT * FROM users WHERE email = 'john@example.com';
   SELECT * FROM email_tokens WHERE user_id = ?;
   ```

5. **Email Verification**
   ```
   Action: Click verification link in email
   Expected:
   - Account activated
   - Redirect to tenant subdomain
   - Onboarding middleware triggers
   ```

**Expected Results**:
- ✅ Tenant database created
- ✅ User account created and verified
- ✅ Redirect to onboarding flow
- ✅ No errors in logs

#### Test Case 1.2: Onboarding Flow
**Objective**: Test complete onboarding process

**Steps**:
1. **Welcome Screen**
   ```
   URL: https://acmecorp.app.com/onboarding
   Expected: Welcome page with progress indicator
   ```

2. **Brand Creation**
   ```
   Data:
   - Brand Name: "Acme SaaS Platform"
   - Description: "Enterprise software solutions"
   - Country: "US"
   - Industry: "Technology"
   
   Expected: Brand created, stored in session
   ```

3. **Plan Selection**
   ```
   Action: Select "Professional" plan
   Data:
   - Currency: USD
   - Billing: Monthly
   
   Expected: 
   - Free trial subscription created
   - Trial period: 14 days
   - Status: 'trial'
   ```

4. **Module Selection**
   ```
   Action: Select modules
   - HR: ✓
   - CRM: ✓
   - Surveys: ✓
   
   Expected: Modules stored in brand metadata
   ```

5. **Completion**
   ```
   Expected:
   - Onboarding marked complete
   - Success page displayed
   - Dashboard access granted
   ```

**Expected Results**:
- ✅ Brand created with metadata
- ✅ Subscription created with trial status
- ✅ Selected modules stored
- ✅ Onboarding completion flag set

#### Test Case 1.3: Dashboard Access
**Objective**: Verify dashboard shows correct tenant/brand data

**Steps**:
1. **Dashboard Load**
   ```
   URL: https://acmecorp.app.com/
   Expected: Dashboard loads successfully
   ```

2. **Verify Tenant Context**
   ```
   Check:
   - Tenant name displayed: "Acme Corp"
   - Brand context: "Acme SaaS Platform"
   - Trial status banner visible
   - Days remaining: 14
   ```

3. **Verify Module Navigation**
   ```
   Sidebar should show:
   - Dashboard
   - HR Management
   - CRM
   - Surveys & Feedback
   - Settings
   
   Should NOT show:
   - Inventory
   - Accounting
   - Project Management
   ```

4. **Verify Trial Information**
   ```
   Check:
   - Trial banner: "14 days remaining"
   - Plan info: "Professional Plan"
   - Upgrade prompts visible
   ```

**Expected Results**:
- ✅ Correct tenant data displayed
- ✅ Only selected modules visible
- ✅ Trial status accurate
- ✅ No data leakage from other tenants

### Scenario 2: Edge Cases & Error Handling

#### Test Case 2.1: Duplicate Registration
**Steps**:
1. Attempt to register with existing email
2. Attempt to register with existing tenant name

**Expected Results**:
- ❌ Validation errors displayed
- ❌ No duplicate records created
- ✅ Clear error messages

#### Test Case 2.2: Incomplete Onboarding
**Steps**:
1. Start onboarding process
2. Close browser after brand creation
3. Return to dashboard URL

**Expected Results**:
- ✅ Redirected back to onboarding
- ✅ Previous progress maintained
- ✅ Can continue from last step

#### Test Case 2.3: Email Verification Delays
**Steps**:
1. Register account
2. Wait 24+ hours
3. Click verification link

**Expected Results**:
- ✅ Account still activates
- ✅ Token remains valid
- ✅ Onboarding flow continues

### Scenario 3: Multi-Tenant Isolation

#### Test Case 3.1: Data Isolation
**Objective**: Ensure tenants cannot access each other's data

**Steps**:
1. Create two separate tenants:
   - Tenant A: "acmecorp"
   - Tenant B: "techstartup"

2. Complete onboarding for both

3. **Test Database Isolation**:
   ```sql
   -- Switch to tenant A database
   USE acmecorp_db;
   SELECT COUNT(*) FROM users; -- Should be 1
   
   -- Switch to tenant B database  
   USE techstartup_db;
   SELECT COUNT(*) FROM users; -- Should be 1
   ```

4. **Test URL Isolation**:
   ```
   acmecorp.app.com → Only Acme Corp data
   techstartup.app.com → Only Tech Startup data
   ```

5. **Test Session Isolation**:
   - Login to Tenant A
   - Try accessing Tenant B URL
   - Should redirect to login

**Expected Results**:
- ✅ Complete data isolation
- ✅ No cross-tenant access
- ✅ Separate user sessions

#### Test Case 3.2: Brand Context Switching
**Objective**: Test multiple brands within same tenant

**Steps**:
1. Create additional brand for existing tenant
2. Verify brand switching functionality
3. Check data context changes

**Expected Results**:
- ✅ Multiple brands per tenant
- ✅ Context switching works
- ✅ Subscription tied to correct brand

### Scenario 4: Subscription & Payment Integration

#### Test Case 4.1: Trial Management
**Steps**:
1. **Active Trial Testing**:
   ```sql
   -- Verify trial subscription
   SELECT * FROM plan_subscriptions 
   WHERE status = 'trial' 
   AND trial_ends_at > NOW();
   ```

2. **Trial Expiration Simulation**:
   ```sql
   -- Simulate trial expiration
   UPDATE plan_subscriptions 
   SET trial_ends_at = DATE_SUB(NOW(), INTERVAL 1 DAY)
   WHERE id = ?;
   ```

3. **Access After Expiration**:
   - Try accessing dashboard
   - Should show payment required

**Expected Results**:
- ✅ Trial status tracked correctly
- ✅ Expiration handled properly
- ✅ Payment prompts appear

#### Test Case 4.2: Payment Gateway Integration
**Steps**:
1. **Setup Test Payment Method**:
   ```
   Card: 4242 4242 4242 4242 (Stripe test)
   Expiry: 12/25
   CVC: 123
   ```

2. **Trial to Paid Conversion**:
   - Add payment method during trial
   - Verify auto-conversion setup
   - Test successful payment

3. **Failed Payment Handling**:
   ```
   Card: 4000 0000 0000 0002 (Declined)
   Expected: Graceful error handling
   ```

**Expected Results**:
- ✅ Payment methods stored securely
- ✅ Successful conversions processed
- ✅ Failed payments handled gracefully

### Scenario 5: Module System Integration

#### Test Case 5.1: Module Activation
**Steps**:
1. **Verify Module Routes**:
   ```php
   // Test module-specific routes
   GET /hr/employees → Should work if HR selected
   GET /inventory/items → Should 404 if not selected
   ```

2. **Database Module Tables**:
   ```sql
   -- Check module-specific tables exist
   SHOW TABLES LIKE 'hr_%';
   SHOW TABLES LIKE 'crm_%';
   ```

3. **Permission System**:
   - Test module-based permissions
   - Verify role restrictions

**Expected Results**:
- ✅ Only selected modules accessible
- ✅ Module tables created correctly
- ✅ Permissions enforced

#### Test Case 5.2: Module Addition/Removal
**Steps**:
1. **Add New Module**:
   - Go to settings
   - Add "Inventory" module
   - Verify navigation updates

2. **Remove Module**:
   - Remove "Surveys" module
   - Verify data preservation
   - Check navigation updates

**Expected Results**:
- ✅ Dynamic module management
- ✅ Data preserved when removing
- ✅ Navigation updates correctly

## Performance Testing

### Load Testing Scenarios

#### Test Case P1: Registration Load
```bash
# Use Apache Bench or similar
ab -n 100 -c 10 https://website.com/api/register

Expected:
- Response time < 2 seconds
- 0% error rate
- Database handles concurrent tenant creation
```

#### Test Case P2: Dashboard Load
```bash
# Test dashboard performance
ab -n 50 -c 5 https://tenant.app.com/

Expected:
- Response time < 1 second
- Proper caching utilized
- Database queries optimized
```

### Database Performance
```sql
-- Check query performance
EXPLAIN SELECT * FROM plan_subscriptions 
WHERE brand_id = ? AND status = 'trial';

-- Verify indexes exist
SHOW INDEX FROM plan_subscriptions;
SHOW INDEX FROM brands;
```

## Security Testing

### Test Case S1: SQL Injection
```php
// Test malicious input
$maliciousInput = "'; DROP TABLE users; --";
// Should be properly escaped/sanitized
```

### Test Case S2: Cross-Tenant Access
```php
// Attempt to access other tenant's data
$response = $this->get('/api/brands/999'); // Other tenant's brand
// Should return 403/404
```

### Test Case S3: Session Security
```php
// Test session hijacking prevention
// Test CSRF protection
// Test XSS prevention
```

## Automated Testing

### Unit Tests
```php
// tests/Unit/OnboardingTest.php
public function test_brand_creation()
{
    $user = User::factory()->create();
    $this->actingAs($user);
    
    $response = $this->post('/onboarding/create-brand', [
        'name' => 'Test Brand',
        'country_code' => 'US',
    ]);
    
    $response->assertRedirect('/onboarding/select-plan');
    $this->assertDatabaseHas('brands', ['name' => 'Test Brand']);
}
```

### Feature Tests
```php
// tests/Feature/CompleteJourneyTest.php
public function test_complete_user_journey()
{
    // Test entire flow from registration to dashboard
    $this->registerUser()
         ->verifyEmail()
         ->completeOnboarding()
         ->accessDashboard()
         ->assertSuccessful();
}
```

### Browser Tests (Laravel Dusk)
```php
// tests/Browser/OnboardingTest.php
public function test_onboarding_flow()
{
    $this->browse(function (Browser $browser) {
        $browser->visit('/onboarding')
                ->type('name', 'Test Brand')
                ->select('country_code', 'US')
                ->press('Continue')
                ->assertPathIs('/onboarding/select-plan');
    });
}
```

## Monitoring & Logging

### Key Metrics to Track
```php
// Log important events
Log::info('User registered', [
    'user_id' => $user->id,
    'tenant_id' => $tenant->id,
    'timestamp' => now(),
]);

Log::info('Onboarding completed', [
    'user_id' => $user->id,
    'brand_id' => $brand->id,
    'selected_modules' => $modules,
    'completion_time' => $completionTime,
]);
```

### Error Tracking
```php
// Monitor critical errors
try {
    $tenant = $this->createTenant($data);
} catch (Exception $e) {
    Log::error('Tenant creation failed', [
        'error' => $e->getMessage(),
        'data' => $data,
        'stack_trace' => $e->getTraceAsString(),
    ]);
    throw $e;
}
```

## Test Checklist

### Pre-Launch Checklist
- [ ] All happy path scenarios pass
- [ ] Error handling works correctly
- [ ] Multi-tenant isolation verified
- [ ] Payment integration tested
- [ ] Module system functional
- [ ] Performance benchmarks met
- [ ] Security vulnerabilities addressed
- [ ] Automated tests passing
- [ ] Monitoring/logging configured
- [ ] Documentation complete

### Post-Launch Monitoring
- [ ] Registration conversion rates
- [ ] Onboarding completion rates
- [ ] Trial to paid conversion
- [ ] Error rates and types
- [ ] Performance metrics
- [ ] User feedback
- [ ] Support ticket analysis

## Troubleshooting Guide

### Common Issues

#### Issue 1: Tenant Creation Fails
```
Symptoms: Registration fails with database error
Causes: 
- Database permissions
- Migration not run
- Duplicate tenant name

Solution:
1. Check database logs
2. Verify migrations
3. Check tenant name uniqueness
```

#### Issue 2: Onboarding Redirect Loop
```
Symptoms: User stuck in onboarding
Causes:
- Middleware misconfiguration
- Session data corruption
- Brand metadata missing

Solution:
1. Clear onboarding session data
2. Check brand metadata structure
3. Verify middleware logic
```

#### Issue 3: Module Navigation Missing
```
Symptoms: Selected modules not showing
Causes:
- Metadata not saved
- Route registration issues
- Permission problems

Solution:
1. Check brand metadata
2. Verify module routes
3. Check user permissions
```

This comprehensive testing guide ensures all aspects of the registration to dashboard journey are thoroughly validated and any issues are caught before production deployment.
