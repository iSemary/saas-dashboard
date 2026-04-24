# Complete User Journey: Registration to Dashboard

## Overview

This document describes the complete user journey from initial registration to accessing the personalized dashboard, including tenant creation, brand setup, plan selection, module customization, and free trial activation.

## Architecture Overview

### Multi-Tenant Structure
```
┌─────────────────┐
│     TENANT      │ (Organization/Company)
│   (Landlord DB) │
└─────────┬───────┘
          │ has many
          ▼
┌─────────────────┐
│     BRAND       │ (Product/Service Line)
│   (Landlord DB) │
└─────────┬───────┘
          │ has many
          ▼
┌─────────────────┐    ┌─────────────────┐
│  SUBSCRIPTION   │◄──►│      USER       │
│   (Landlord DB) │    │   (Tenant DB)   │
└─────────────────┘    └─────────────────┘
```

### Database Distribution
- **Landlord Database**: Tenants, Brands, Subscriptions, Plans, Payments
- **Tenant Database**: Users, Application Data, Module-specific Data

## Complete User Journey

### Phase 1: Registration & Tenant Creation

#### Step 1: User Registration
**Location**: `saas-website` (Marketing Site)
**Endpoint**: `POST /api/register`
**Controller**: `App\Http\Controllers\Api\RegistrationController@register`

**Process**:
1. User fills registration form with:
   - Personal details (name, email, password)
   - Company details (customer_title, customer_username)
   - Location (country_id)
   - Business category (category_id)

2. **Backend Processing**:
   ```php
   // RegistrationService::register()
   DB::beginTransaction();
   
   // 1. Format tenant username
   $customerUsername = TenantHelper::format($userRequest['customer_username']);
   
   // 2. Create tenant with dedicated database
   $tenant = $this->tenantRepository->init($customerUsername);
   
   // 3. Create customer record (landlord DB)
   $customer = $this->customerRepository->create($customerData);
   
   // 4. Switch to tenant database
   $tenant = TenantHelper::makeCurrent($customerUsername);
   
   // 5. Create user in tenant database
   $user = $tenant->execute(function () use ($userRequest) {
       return $this->userRepository->create($userRequest);
   });
   
   // 6. Create email verification token
   $token = $tenant->execute(function () use ($user) {
       return EmailToken::createToken($user->id);
   });
   
   DB::commit();
   ```

3. **Email Verification Sent**:
   - User receives verification email
   - Email contains verification link: `{tenant_url}/verify/email?token={token}`

#### Step 2: Email Verification
**Process**:
1. User clicks verification link
2. System verifies token and activates account
3. User is redirected to tenant subdomain
4. **Onboarding middleware** detects no completed brands → redirects to onboarding

### Phase 2: Onboarding Flow

#### Step 1: Welcome Screen
**Route**: `/onboarding`
**View**: `landlord.onboarding.welcome`
**Controller**: `OnboardingController@welcome`

**Features**:
- Welcome message with user's name
- Overview of 3-step process
- Visual progress indicators
- Call-to-action to start onboarding

#### Step 2: Brand Creation
**Route**: `/onboarding/create-brand`
**View**: `landlord.onboarding.create-brand`
**Controller**: `OnboardingController@createBrand` / `storeBrand`

**Form Fields**:
- Brand Name (required)
- Description (optional)
- Primary Country (required)
- Industry (optional)

**Backend Process**:
```php
// OnboardingController::storeBrand()
$brand = Brand::create([
    'name' => $request->name,
    'slug' => Str::slug($request->name),
    'description' => $request->description,
    'tenant_id' => session('tenant_id'),
    'created_by' => auth()->id(),
]);

session(['onboarding_brand_id' => $brand->id]);
```

#### Step 3: Plan Selection
**Route**: `/onboarding/select-plan`
**View**: `landlord.onboarding.select-plan`
**Controller**: `OnboardingController@selectPlan` / `storePlan`

**Available Plans**:
1. **Starter Plan**
   - 5 team members
   - 10 GB storage
   - 10 projects
   - Email support
   - Basic reports

2. **Professional Plan** (Most Popular)
   - 25 team members
   - 100 GB storage
   - 100 projects
   - Priority support
   - Advanced reports
   - API access
   - Integrations
   - Custom branding

3. **Enterprise Plan**
   - Unlimited team members
   - Unlimited storage
   - Unlimited projects
   - Dedicated support manager
   - Advanced analytics
   - Full API access
   - White label solution
   - SSO & audit logs

**Free Trial Configuration**:
- **Duration**: 14 days for all plans
- **Access**: Full plan features during trial
- **Requirements**: No credit card required
- **Conversion**: Auto-converts to paid after trial (if payment method added)

**Backend Process**:
```php
// OnboardingController::storePlan()
$trial = $plan->getTrialFor();
$trialDays = $trial ? $trial->trial_days : 14;

$subscription = PlanSubscription::create([
    'subscription_id' => 'sub_' . uniqid() . '_' . time(),
    'brand_id' => $brandId,
    'user_id' => auth()->id(),
    'plan_id' => $plan->id,
    'currency_id' => $currency->id,
    'price' => 0, // Free during trial
    'billing_cycle' => $request->billing_cycle,
    'trial_starts_at' => now(),
    'trial_ends_at' => now()->addDays($trialDays),
    'status' => 'trial',
    'auto_renew' => 'enabled',
]);
```

#### Step 4: Module Selection
**Route**: `/onboarding/select-modules`
**View**: `landlord.onboarding.select-modules`
**Controller**: `OnboardingController@selectModules` / `storeModules`

**Available Modules**:
1. **Human Resources (HR)**
   - Employee Records
   - Payroll Management
   - Attendance Tracking
   - Performance Reviews

2. **Customer Relationship Management (CRM)**
   - Lead Management
   - Contact Management
   - Sales Pipeline
   - Customer Support

3. **Surveys & Feedback**
   - Survey Builder
   - Response Collection
   - Analytics Dashboard
   - Report Generation

4. **Inventory Management**
   - Stock Tracking
   - Warehouse Management
   - Purchase Orders
   - Supplier Management

5. **Accounting & Finance**
   - Financial Reports
   - Invoicing
   - Expense Tracking
   - Tax Management

6. **Project Management**
   - Task Management
   - Project Tracking
   - Team Collaboration
   - Time Tracking

**Backend Process**:
```php
// OnboardingController::storeModules()
$brand->update([
    'metadata' => [
        'selected_modules' => $request->modules,
        'onboarding_completed' => true,
        'onboarding_completed_at' => now(),
    ]
]);

$subscription->update([
    'subscription_data' => [
        'selected_modules' => $request->modules,
        'onboarding_completed' => true,
    ]
]);
```

#### Step 5: Completion
**Route**: `/onboarding/complete`
**View**: `landlord.onboarding.complete`
**Controller**: `OnboardingController@complete`

**Features**:
- Success animation and confirmation
- Summary of completed setup
- Next steps guidance
- Links to documentation and support
- Call-to-action to access dashboard

### Phase 3: Dashboard Access

#### Dashboard Redirect
**Route**: `/onboarding/complete` (POST)
**Controller**: `OnboardingController@redirectToDashboard`

**Process**:
1. Clear onboarding session data
2. Redirect to main dashboard: `route('home')`
3. Success message: "Welcome to your dashboard! Your free trial has started."

#### Dashboard Features
**Route**: `/` (home)
**Middleware**: `CheckOnboarding` (ensures onboarding completed)

**Dashboard Components**:
1. **Tenant-Specific Data**: Only shows data for current tenant
2. **Brand Context**: User can switch between brands within tenant
3. **Module Navigation**: Sidebar shows only selected modules
4. **Trial Status**: Banner showing trial days remaining
5. **Quick Actions**: Based on selected modules

## Technical Implementation

### Database Schema

#### Tenants Table (Landlord DB)
```sql
CREATE TABLE tenants (
    id BIGINT PRIMARY KEY,
    name VARCHAR(255) UNIQUE,
    domain VARCHAR(255),
    database VARCHAR(255),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

#### Brands Table (Landlord DB)
```sql
CREATE TABLE brands (
    id BIGINT PRIMARY KEY,
    name VARCHAR(255),
    slug VARCHAR(255),
    description TEXT,
    tenant_id BIGINT FOREIGN KEY REFERENCES tenants(id),
    metadata JSON, -- Contains onboarding_completed, selected_modules
    created_by BIGINT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    deleted_at TIMESTAMP
);
```

#### Plan Subscriptions Table (Landlord DB)
```sql
CREATE TABLE plan_subscriptions (
    id BIGINT PRIMARY KEY,
    subscription_id VARCHAR(255) UNIQUE,
    brand_id BIGINT FOREIGN KEY REFERENCES brands(id),
    user_id BIGINT,
    plan_id BIGINT FOREIGN KEY REFERENCES plans(id),
    currency_id BIGINT FOREIGN KEY REFERENCES currencies(id),
    price DECIMAL(10,2),
    billing_cycle ENUM('monthly', 'quarterly', 'annually'),
    trial_starts_at TIMESTAMP,
    trial_ends_at TIMESTAMP,
    starts_at TIMESTAMP,
    ends_at TIMESTAMP,
    next_billing_at TIMESTAMP,
    status ENUM('trial', 'active', 'past_due', 'canceled', 'expired'),
    subscription_data JSON, -- Contains selected_modules
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Middleware & Security

#### CheckOnboarding Middleware
```php
// Redirects to onboarding if not completed
public function handle(Request $request, Closure $next)
{
    $tenantId = session('tenant_id');
    if ($tenantId) {
        $completedBrands = Brand::where('tenant_id', $tenantId)
            ->whereNotNull('metadata->onboarding_completed')
            ->count();

        if ($completedBrands === 0) {
            return redirect()->route('onboarding.welcome');
        }
    }
    return $next($request);
}
```

#### Tenant Context
- **Session Management**: `tenant_id` stored in session
- **Database Switching**: Dynamic database connection based on tenant
- **URL Structure**: `{tenant}.domain.com` or `domain.com/{tenant}`

### Payment Integration

#### Free Trial Management
```php
// Trial expiration check
public function isTrialExpired()
{
    return $this->status === 'trial' && 
           $this->trial_ends_at && 
           $this->trial_ends_at->isPast();
}

// Auto-conversion to paid
public function convertToPaid()
{
    if ($this->isTrialExpired()) {
        $this->update([
            'status' => 'active',
            'price' => $this->plan->getPriceFor($this->currency->code),
            'starts_at' => now(),
            'ends_at' => now()->addMonth(),
        ]);
    }
}
```

#### Payment Gateway Integration
- **Supported Gateways**: Stripe, PayPal, Razorpay, Adyen, Square
- **Payment Methods**: Credit cards, bank transfers, digital wallets
- **Billing Automation**: Automatic recurring billing after trial
- **Dunning Management**: Failed payment retry logic

## User Experience Flow

### Visual Flow Diagram
```
Registration → Email Verification → Onboarding Welcome
     ↓                ↓                    ↓
Tenant Creation → Account Activation → Brand Creation
     ↓                ↓                    ↓
Database Setup → Login Redirect → Plan Selection (Free Trial)
     ↓                ↓                    ↓
User Creation → Onboarding Check → Module Selection
     ↓                ↓                    ↓
Email Sent → Dashboard Access → Setup Complete → Dashboard
```

### Success Metrics
1. **Registration Completion**: User successfully creates account
2. **Email Verification**: User verifies email within 24 hours
3. **Onboarding Completion**: User completes all 3 onboarding steps
4. **Trial Activation**: Free trial successfully starts
5. **Module Selection**: User selects at least 1 module
6. **Dashboard Access**: User successfully accesses personalized dashboard

### Error Handling
1. **Registration Failures**: Validation errors, duplicate usernames
2. **Database Issues**: Tenant creation failures, connection problems
3. **Email Delivery**: Verification email delivery failures
4. **Onboarding Abandonment**: Users leaving mid-process
5. **Payment Setup**: Credit card validation, gateway errors

## Testing Scenarios

### Happy Path Testing
1. **Complete Registration Flow**:
   - Register new user
   - Verify email
   - Complete onboarding
   - Access dashboard

2. **Plan Selection Testing**:
   - Test all plan options
   - Verify trial activation
   - Check feature access

3. **Module Integration**:
   - Select different module combinations
   - Verify dashboard customization
   - Test module functionality

### Edge Cases
1. **Incomplete Onboarding**: User leaves mid-process
2. **Email Verification Delays**: Late verification attempts
3. **Multiple Brand Creation**: Creating additional brands
4. **Plan Changes**: Upgrading/downgrading during trial
5. **Trial Expiration**: Handling trial end scenarios

## Monitoring & Analytics

### Key Performance Indicators (KPIs)
1. **Registration Conversion Rate**: Visitors → Registered Users
2. **Email Verification Rate**: Registered → Verified Users
3. **Onboarding Completion Rate**: Verified → Onboarded Users
4. **Trial-to-Paid Conversion**: Trial → Paying Customers
5. **Module Adoption Rate**: Popular module combinations

### Tracking Events
```javascript
// Analytics tracking points
analytics.track('user_registered', { tenant_id, user_id });
analytics.track('email_verified', { tenant_id, user_id });
analytics.track('brand_created', { tenant_id, brand_id, user_id });
analytics.track('plan_selected', { plan_id, billing_cycle, user_id });
analytics.track('modules_selected', { modules, user_id });
analytics.track('onboarding_completed', { user_id, completion_time });
analytics.track('dashboard_accessed', { user_id, first_access: true });
```

## Future Enhancements

### Planned Features
1. **Guided Tours**: Interactive dashboard tutorials
2. **Smart Recommendations**: AI-powered module suggestions
3. **Team Invitations**: Invite team members during onboarding
4. **Data Import**: Import existing data during setup
5. **Custom Branding**: Logo and color customization
6. **Advanced Analytics**: Detailed usage tracking and insights

### Scalability Considerations
1. **Database Sharding**: Distribute tenant databases across servers
2. **Caching Strategy**: Redis for session and frequently accessed data
3. **CDN Integration**: Static assets and file storage
4. **Load Balancing**: Distribute traffic across multiple servers
5. **Monitoring**: Application performance and error tracking

---

## Conclusion

This comprehensive user journey ensures a smooth transition from initial interest to active platform usage, with a focus on user experience, technical reliability, and business growth. The multi-tenant architecture supports scalability while maintaining data isolation and security.

The free trial approach reduces friction for new users while the modular system allows for customized experiences based on business needs. The brand-centric subscription model aligns with real-world business structures and supports future growth scenarios.
