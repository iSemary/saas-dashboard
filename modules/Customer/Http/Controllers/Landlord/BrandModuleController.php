<?php

namespace Modules\Customer\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use Modules\Customer\Http\Requests\BrandModuleSubscriptionFormRequest;
use Modules\Customer\Services\BrandModuleSubscriptionService;
use Modules\Customer\Services\BrandService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class BrandModuleController extends Controller
{
    protected BrandModuleSubscriptionService $brandModuleService;
    protected BrandService $brandService;

    public function __construct(
        BrandModuleSubscriptionService $brandModuleService,
        BrandService $brandService
    ) 
    {
        $this->brandModuleService = $brandModuleService;
        $this->brandService = $brandService;
    }

    /**
     * Display listing of brand module subscriptions.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['brand_id', 'module_key', 'subscription_status', 'search', 'date_from', 'date_to']);
            $perPage = $request->get('per_page', 15);
            
            $subscriptions = $this->brandModuleService->getAll($filters, $perPage);

            return response()->json([
                'success' => true,
                'data' => $subscriptions->items(),
                'pagination' => [
                    'current_page' => $subscriptions->currentPage(),
                    'last_page' => $subscriptions->lastPage(),
                    'per_page' => $subscriptions->perPage(),
                    'total' => $subscriptions->total(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => translate('failed_to_retrieve_subscriptions'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get DataTables data for brand module subscriptions.
     */
    public function getDataTables(): JsonResponse
    {
        try {
            $data = $this->brandModuleService->getDataTables();
            return $data;
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => translate('failed_to_load_data'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show brand module selection interface.
     */
    public function showBrandModules(int $brandId)
    {
        try {
            $brand = $this->brandService->getById($brandId);
            if (!$brand) 
            {
                return redirect()->route('landlord.brands.index')
                               ->with('error', translate('brand_not_found'));
            }

            $activeSubscriptions = $this->brandModuleService->getActiveSubscriptions($brandId);
            $availableModules = $this->brandModuleService->getAvailableModules();
            
            // Check which modules are already subscribed
            $subscribedModules = $activeSubscriptions->pluck('module_key')->toArray();
            $unsubscribedModules = array_diff_key($availableModules, array_flip($subscribedModules));

            return view('landlord.customer.brands.modules.index', compact(
                'brand', 
                'activeSubscriptions', 
                'availableModules',
                'subscribedModules',
                'unsubscribedModules'
            ));
        } catch (\Exception $e) {
            return redirect()->route('landlord.brands.index')
                           ->with('error', translate('error_loading_brand_modules') . ': ' . $e->getMessage());
        }
    }

    /**
     * Subscribe brand to selected modules.
     */
    public function subscribe(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'brand_id' => 'required|integer|exists:brands,id',
                'module_keys' => 'required|array',
                'module_keys.*' => 'string|in:' . implode(',', array_keys($this->brandModuleService->getAvailableModules())),
                'module_configs' => 'sometimes|array',
            ]);

            $brandId = $request->brand_id;
            $moduleKeys = $request->module_keys;
            $moduleConfigs = $request->get('module_configs', []);

            // Check if brand already has active subscriptions for these modules
            $existingSubscriptions = $this->brandModuleService->getActiveSubscriptions($brandId)
                                                             ->pluck('module_key')
                                                             ->toArray();
            
            $duplicateModules = array_intersect($moduleKeys, $existingSubscriptions);
            if (!empty($duplicateModules)) 
            {
                return response()->json([
                    'success' => false,
                    'message' => translate('brand_already_subscribed_to_modules') . ': ' . implode(', ', $duplicateModules),
                ], 400);
            }

            $subscriptions = $this->brandModuleService->subscribeToModules($brandId, $moduleKeys, $moduleConfigs);

            return response()->json([
                'success' => true,
                'message' => translate('brand_successfully_subscribed_to_modules'),
                'data' => $subscriptions
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => translate('validation_error'),
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => translate('failed_to_subscribe_brand'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Unsubscribe brand from selected modules.
     */
    public function unsubscribe(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'brand_id' => 'required|integer|exists:brands,id',
                'module_keys' => 'required|array',
                'module_keys.*' => 'string|exists:brand_module_subscriptions,module_key',
            ]);

            $brandId = $request->brand_id;
            $moduleKeys = $request->module_keys;

            $subscriptions = $this->brandModuleService->unsubscribeFromModules($brandId, $moduleKeys);

            return response()->json([
                'success' => true,
                'message' => translate('brand_successfully_unsubscribed_from_modules'),
                'data' => $subscriptions
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => translate('validation_error'),
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => translate('failed_to_unsubscribe_brand'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle subscription status.
     */
    public function toggleStatus(int $id): JsonResponse
    {
        try {
            $success = $this->brandModuleService->toggleSubscriptionStatus($id);
            
            if ($success) 
            {
                $subscription = $this->brandModuleService->getById($id);
                return response()->json([
                    'success' => true,
                    'message' => translate('subscription_status_updated'),
                    'data' => [
                        'status' => $subscription->subscription_status,
                        'status_badge' => '<span class="badge ' . $subscription->getStatusBadgeClass() . '">' . 
                                         ucfirst($subscription->subscription_status) . 
                                         '</span>'
                    ]
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => translate('failed_to_update_subscription_status'),
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => translate('error_occurred'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show specific module dashboard for brand.
     */
    public function showModuleDashboard(int $brandId, string $moduleKey)
    {
        try {
            $brand = $this->brandService->getById($brandId);
            if (!$brand) 
            {
                return redirect()->route('landlord.brands.index')
                               ->with('error', translate('brand_not_found'));
            }

            // Check if brand has access to this module
            if (!$this->brandModuleService->hasActiveSubscription($brandId, $moduleKey)) 
            {
                return redirect()->route('landlord.brands.modules.show', $brandId)
                               ->with('error', translate('brand_no_access_to_module') . ': ' . $moduleKey);
            }

            $subscription = $this->brandModuleService->getByBrandAndModule($brandId, $moduleKey);
            $availableModules = $this->brandModuleService->getAvailableModules();
            $moduleInfo = $availableModules[$moduleKey] ?? null;

            if (!$moduleInfo) 
            {
                return redirect()->route('landlord.brands.modules.show', $brandId)
                               ->with('error', translate('module_not_found') . ': ' . $moduleKey);
            }

            // Redirect to module-specific dashboard controller
            $routeName = 'landlord.' . $moduleKey . '.dashboard.show';
            return redirect()->route($routeName, ['brandId' => $brandId]);
            
        } catch (\Exception $e) {
            return redirect()->route('landlord.brands.index')
                           ->with('error', translate('error_loading_module_dashboard') . ': ' . $e->getMessage());
        }
    }

    /**
     * Get dashboard statistics.
     */
    public function getDashboardStats(): JsonResponse
    {
        try {
            $stats = $this->brandModuleService->getDashboardStats();
            
            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => translate('failed_to_load_dashboard_stats'),
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
