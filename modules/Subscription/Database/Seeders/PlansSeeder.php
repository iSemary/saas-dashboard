<?php

namespace Modules\Subscription\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Subscription\Entities\Plan;
use Modules\Subscription\Entities\PlanFeature;
use Modules\Subscription\Entities\PlanTrial;
use Modules\Utilities\Entities\Currency;

class PlansSeeder extends Seeder
{
    public function run(): void
    {
        // Get USD currency
        $usdCurrency = Currency::where('code', 'USD')->first();
        if (!$usdCurrency) {
            $usdCurrency = Currency::create([
                'code' => 'USD',
                'name' => 'US Dollar',
                'symbol' => '$',
                'decimal_places' => 2,
                'is_active' => true,
                'conversion_rate' => 1.00,
            ]);
        }

        // Create Starter Plan
        $starterPlan = Plan::create([
            'name' => 'Starter',
            'slug' => 'starter',
            'description' => 'Perfect for small businesses and startups',
            'features_summary' => 'Essential features to get you started',
            'sort_order' => 1,
            'is_popular' => false,
            'is_custom' => false,
            'status' => 'active',
        ]);

        // Create Professional Plan (Popular)
        $proPlan = Plan::create([
            'name' => 'Professional',
            'slug' => 'professional',
            'description' => 'Ideal for growing businesses with advanced needs',
            'features_summary' => 'Advanced features for scaling businesses',
            'sort_order' => 2,
            'is_popular' => true,
            'is_custom' => false,
            'status' => 'active',
        ]);

        // Create Enterprise Plan
        $enterprisePlan = Plan::create([
            'name' => 'Enterprise',
            'slug' => 'enterprise',
            'description' => 'Comprehensive solution for large organizations',
            'features_summary' => 'Full-featured platform with premium support',
            'sort_order' => 3,
            'is_popular' => false,
            'is_custom' => false,
            'status' => 'active',
        ]);

        // Add features for Starter Plan
        $this->addPlanFeatures($starterPlan, [
            ['key' => 'users', 'name' => 'Team Members', 'type' => 'numeric', 'limit' => 5, 'unit' => 'users'],
            ['key' => 'storage', 'name' => 'Storage Space', 'type' => 'numeric', 'limit' => 10, 'unit' => 'GB'],
            ['key' => 'projects', 'name' => 'Projects', 'type' => 'numeric', 'limit' => 10, 'unit' => 'projects'],
            ['key' => 'basic_support', 'name' => 'Email Support', 'type' => 'boolean', 'value' => true],
            ['key' => 'basic_reporting', 'name' => 'Basic Reports', 'type' => 'boolean', 'value' => true],
        ]);

        // Add features for Professional Plan
        $this->addPlanFeatures($proPlan, [
            ['key' => 'users', 'name' => 'Team Members', 'type' => 'numeric', 'limit' => 25, 'unit' => 'users'],
            ['key' => 'storage', 'name' => 'Storage Space', 'type' => 'numeric', 'limit' => 100, 'unit' => 'GB'],
            ['key' => 'projects', 'name' => 'Projects', 'type' => 'numeric', 'limit' => 100, 'unit' => 'projects'],
            ['key' => 'priority_support', 'name' => 'Priority Support', 'type' => 'boolean', 'value' => true],
            ['key' => 'advanced_reporting', 'name' => 'Advanced Reports', 'type' => 'boolean', 'value' => true],
            ['key' => 'api_access', 'name' => 'API Access', 'type' => 'boolean', 'value' => true],
            ['key' => 'integrations', 'name' => 'Third-party Integrations', 'type' => 'boolean', 'value' => true],
            ['key' => 'custom_branding', 'name' => 'Custom Branding', 'type' => 'boolean', 'value' => true],
        ]);

        // Add features for Enterprise Plan
        $this->addPlanFeatures($enterprisePlan, [
            ['key' => 'users', 'name' => 'Team Members', 'type' => 'numeric', 'unlimited' => true, 'unit' => 'users'],
            ['key' => 'storage', 'name' => 'Storage Space', 'type' => 'numeric', 'unlimited' => true, 'unit' => 'GB'],
            ['key' => 'projects', 'name' => 'Projects', 'type' => 'numeric', 'unlimited' => true, 'unit' => 'projects'],
            ['key' => 'dedicated_support', 'name' => 'Dedicated Support Manager', 'type' => 'boolean', 'value' => true],
            ['key' => 'advanced_reporting', 'name' => 'Advanced Reports & Analytics', 'type' => 'boolean', 'value' => true],
            ['key' => 'api_access', 'name' => 'Full API Access', 'type' => 'boolean', 'value' => true],
            ['key' => 'integrations', 'name' => 'All Integrations', 'type' => 'boolean', 'value' => true],
            ['key' => 'custom_branding', 'name' => 'White Label Solution', 'type' => 'boolean', 'value' => true],
            ['key' => 'sso', 'name' => 'Single Sign-On (SSO)', 'type' => 'boolean', 'value' => true],
            ['key' => 'audit_logs', 'name' => 'Audit Logs', 'type' => 'boolean', 'value' => true],
        ]);

        // Add trial configurations
        $this->addTrialConfigurations([$starterPlan, $proPlan, $enterprisePlan]);
    }

    private function addPlanFeatures(Plan $plan, array $features)
    {
        foreach ($features as $index => $feature) {
            PlanFeature::create([
                'plan_id' => $plan->id,
                'feature_key' => $feature['key'],
                'name' => $feature['name'],
                'description' => $feature['description'] ?? null,
                'feature_type' => $feature['type'],
                'feature_value' => isset($feature['value']) ? (string) $feature['value'] : null,
                'numeric_limit' => $feature['limit'] ?? null,
                'is_unlimited' => $feature['unlimited'] ?? false,
                'unit' => $feature['unit'] ?? null,
                'sort_order' => $index + 1,
                'is_highlighted' => $index < 3, // Highlight first 3 features
                'status' => 'active',
            ]);
        }
    }

    private function addTrialConfigurations(array $plans)
    {
        foreach ($plans as $plan) {
            PlanTrial::create([
                'plan_id' => $plan->id,
                'country_code' => null, // Global trial
                'trial_days' => 14,
                'requires_payment_method' => false,
                'auto_convert' => true,
                'trial_type' => 'free',
                'trial_price' => 0,
                'trial_features' => 'Full access to all plan features',
                'trial_limits' => json_encode([
                    'max_projects' => 3,
                    'max_team_members' => 2,
                ]),
                'trial_terms' => 'No credit card required. Cancel anytime during trial.',
                'allow_multiple_trials' => false,
                'grace_period_days' => 3,
                'status' => 'active',
            ]);
        }
    }
}
