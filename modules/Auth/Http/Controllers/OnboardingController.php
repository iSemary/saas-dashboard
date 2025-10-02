<?php

namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Customer\Entities\Brand;
use Modules\Subscription\Entities\Plan;
use Modules\Subscription\Entities\PlanSubscription;
use Modules\Utilities\Entities\Currency;
use Modules\Geography\Entities\Country;
use Modules\Tenant\Helper\TenantHelper;

class OnboardingController extends Controller
{
    /**
     * Show the onboarding welcome page
     */
    public function welcome()
    {
        $user = auth()->user();
        
        // Check if user already has brands
        $existingBrands = Brand::where('tenant_id', session('tenant_id'))->count();
        
        if ($existingBrands > 0) {
            return redirect()->route('home');
        }

        return view('landlord.onboarding.welcome', compact('user'));
    }

    /**
     * Show brand creation step
     */
    public function createBrand()
    {
        $user = auth()->user();
        $countries = Country::active()->get();
        
        return view('landlord.onboarding.create-brand', compact('user', 'countries'));
    }

    /**
     * Store the brand
     */
    public function storeBrand(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'country_code' => 'required|string|size:2',
            'industry' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            $brand = Brand::create([
                'name' => $request->name,
                'slug' => \Str::slug($request->name),
                'description' => $request->description,
                'tenant_id' => session('tenant_id'),
                'created_by' => auth()->id(),
            ]);

            // Store brand in session for next steps
            session(['onboarding_brand_id' => $brand->id]);

            DB::commit();

            return redirect()->route('onboarding.select-plan');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to create brand: ' . $e->getMessage()]);
        }
    }

    /**
     * Show plan selection step
     */
    public function selectPlan()
    {
        $brandId = session('onboarding_brand_id');
        if (!$brandId) {
            return redirect()->route('onboarding.create-brand');
        }

        $brand = Brand::find($brandId);
        $plans = Plan::active()->with(['features', 'prices', 'trials'])->ordered()->get();
        $currencies = Currency::active()->get();
        
        return view('landlord.onboarding.select-plan', compact('brand', 'plans', 'currencies'));
    }

    /**
     * Store plan selection and create subscription
     */
    public function storePlan(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'currency_id' => 'required|exists:currencies,id',
            'billing_cycle' => 'required|in:monthly,quarterly,annually',
        ]);

        $brandId = session('onboarding_brand_id');
        if (!$brandId) {
            return redirect()->route('onboarding.create-brand');
        }

        try {
            DB::beginTransaction();

            $plan = Plan::find($request->plan_id);
            $currency = Currency::find($request->currency_id);
            
            // Get trial configuration
            $trial = $plan->getTrialFor();
            $trialDays = $trial ? $trial->trial_days : 14; // Default 14 days

            // Create subscription with free trial
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
                'starts_at' => now()->addDays($trialDays),
                'ends_at' => now()->addDays($trialDays)->addMonth(), // First billing after trial
                'next_billing_at' => now()->addDays($trialDays),
                'status' => 'trial',
                'auto_renew' => 'enabled',
            ]);

            session(['onboarding_subscription_id' => $subscription->id]);

            DB::commit();

            return redirect()->route('onboarding.select-modules');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to create subscription: ' . $e->getMessage()]);
        }
    }

    /**
     * Show module selection step
     */
    public function selectModules()
    {
        $brandId = session('onboarding_brand_id');
        $subscriptionId = session('onboarding_subscription_id');
        
        if (!$brandId || !$subscriptionId) {
            return redirect()->route('onboarding.create-brand');
        }

        $brand = Brand::find($brandId);
        $subscription = PlanSubscription::find($subscriptionId);
        
        // Available modules
        $availableModules = [
            'hr' => [
                'name' => 'Human Resources',
                'description' => 'Employee management, payroll, attendance tracking',
                'icon' => 'fas fa-users',
                'features' => ['Employee Records', 'Payroll Management', 'Attendance Tracking', 'Performance Reviews']
            ],
            'crm' => [
                'name' => 'Customer Relationship Management',
                'description' => 'Lead management, customer tracking, sales pipeline',
                'icon' => 'fas fa-handshake',
                'features' => ['Lead Management', 'Contact Management', 'Sales Pipeline', 'Customer Support']
            ],
            'surveys' => [
                'name' => 'Surveys & Feedback',
                'description' => 'Create surveys, collect feedback, analyze responses',
                'icon' => 'fas fa-poll',
                'features' => ['Survey Builder', 'Response Collection', 'Analytics Dashboard', 'Report Generation']
            ],
            'inventory' => [
                'name' => 'Inventory Management',
                'description' => 'Stock tracking, warehouse management, supply chain',
                'icon' => 'fas fa-boxes',
                'features' => ['Stock Tracking', 'Warehouse Management', 'Purchase Orders', 'Supplier Management']
            ],
            'accounting' => [
                'name' => 'Accounting & Finance',
                'description' => 'Financial reporting, invoicing, expense tracking',
                'icon' => 'fas fa-calculator',
                'features' => ['Financial Reports', 'Invoicing', 'Expense Tracking', 'Tax Management']
            ],
            'project_management' => [
                'name' => 'Project Management',
                'description' => 'Task management, project tracking, team collaboration',
                'icon' => 'fas fa-tasks',
                'features' => ['Task Management', 'Project Tracking', 'Team Collaboration', 'Time Tracking']
            ]
        ];
        
        return view('landlord.onboarding.select-modules', compact('brand', 'subscription', 'availableModules'));
    }

    /**
     * Store module selection and complete onboarding
     */
    public function storeModules(Request $request)
    {
        $request->validate([
            'modules' => 'required|array|min:1',
            'modules.*' => 'string|in:hr,crm,surveys,inventory,accounting,project_management',
        ]);

        $brandId = session('onboarding_brand_id');
        $subscriptionId = session('onboarding_subscription_id');
        
        if (!$brandId || !$subscriptionId) {
            return redirect()->route('onboarding.create-brand');
        }

        try {
            DB::beginTransaction();

            $brand = Brand::find($brandId);
            $subscription = PlanSubscription::find($subscriptionId);

            // Store selected modules in brand metadata
            $brand->update([
                'metadata' => [
                    'selected_modules' => $request->modules,
                    'onboarding_completed' => true,
                    'onboarding_completed_at' => now(),
                ]
            ]);

            // Store modules in subscription metadata
            $subscription->update([
                'subscription_data' => [
                    'selected_modules' => $request->modules,
                    'onboarding_completed' => true,
                ]
            ]);

            // Clear onboarding session data
            session()->forget(['onboarding_brand_id', 'onboarding_subscription_id']);

            DB::commit();

            return redirect()->route('onboarding.complete');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to save modules: ' . $e->getMessage()]);
        }
    }

    /**
     * Show onboarding completion page
     */
    public function complete()
    {
        return view('landlord.onboarding.complete');
    }

    /**
     * Redirect to dashboard after onboarding
     */
    public function redirectToDashboard()
    {
        return redirect()->route('home')->with('success', 'Welcome to your dashboard! Your free trial has started.');
    }
}
