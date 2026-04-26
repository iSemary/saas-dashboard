<?php

namespace Modules\Subscription\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Subscription\DTOs\AddModuleData;
use Modules\Subscription\DTOs\ChangePlanData;
use Modules\Subscription\DTOs\RemoveModuleData;
use Modules\Subscription\DTOs\SubscribeToPlanData;
use Modules\Subscription\Services\InvoiceGenerationService;
use Modules\Subscription\Services\ModuleAddonService;
use Modules\Subscription\Services\PaymentChargeService;
use Modules\Subscription\Services\ProrationCalculator;
use Modules\Subscription\Services\TenantBillingService;

class TenantBillingApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(
        protected TenantBillingService $billingService,
        protected ModuleAddonService $moduleService,
        protected PaymentChargeService $paymentService,
        protected InvoiceGenerationService $invoiceService,
        protected ProrationCalculator $prorationCalculator,
    ) {}

    /**
     * Get billing overview for current tenant.
     */
    public function overview(Request $request)
    {
        $brandId = $this->getBrandId($request);
        $overview = $this->billingService->getBillingOverview($brandId);
        return $this->apiSuccess($overview);
    }

    /**
     * Get available plans.
     */
    public function plans(Request $request)
    {
        $brandId = $this->getBrandId($request);
        $currency = $request->get('currency');
        $plans = $this->billingService->getAvailablePlans($brandId, $currency);
        return $this->apiSuccess($plans);
    }

    /**
     * Subscribe to a plan.
     */
    public function subscribeToPlan(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|integer',
            'gateway' => 'required|in:stripe,paypal,mock',
            'currency_id' => 'required|integer',
            'billing_cycle' => 'nullable|in:monthly,quarterly,semi_annually,annually,biennially',
            'success_url' => 'nullable|url',
            'cancel_url' => 'nullable|url',
        ]);

        $brandId = $this->getBrandId($request);
        $data = SubscribeToPlanData::fromRequest($request);

        // TODO: Implement plan subscription logic via checkout
        // This would create an invoice and return a checkout session URL

        return $this->apiSuccess([
            'message' => translate('message.use_checkout_flow'),
            'checkout_url' => null,
        ]);
    }

    /**
     * Change current plan.
     */
    public function changePlan(Request $request)
    {
        $request->validate([
            'new_plan_id' => 'required|integer',
            'immediate' => 'nullable|boolean',
            'prorate' => 'nullable|boolean',
        ]);

        $brandId = $this->getBrandId($request);
        $data = ChangePlanData::fromRequest($request);

        // TODO: Implement plan change logic

        return $this->apiSuccess([
            'message' => translate('message.plan_change_initiated'),
        ]);
    }

    /**
     * Cancel current plan.
     */
    public function cancelPlan(Request $request)
    {
        $request->validate([
            'at_period_end' => 'nullable|boolean',
            'reason' => 'nullable|string',
        ]);

        $brandId = $this->getBrandId($request);

        // TODO: Implement plan cancellation logic

        return $this->apiSuccess([
            'message' => translate('message.plan_cancellation_initiated'),
        ]);
    }

    /**
     * Get available add-on modules.
     */
    public function modules(Request $request)
    {
        $brandId = $this->getBrandId($request);
        $modules = $this->billingService->getAvailableModules($brandId);
        return $this->apiSuccess($modules);
    }

    /**
     * Subscribe to an add-on module.
     */
    public function subscribeToModule(Request $request)
    {
        $request->validate([
            'module_id' => 'required|integer',
            'billing_cycle' => 'nullable|in:monthly,quarterly,semi_annually,annually,biennially',
            'immediate' => 'nullable|boolean',
        ]);

        $brandId = $this->getBrandId($request);
        $data = AddModuleData::fromRequest($request);

        $result = $this->moduleService->subscribeToModule($brandId, $data);

        return $this->apiSuccess($result, translate('message.subscription_success'));
    }

    /**
     * Unsubscribe from an add-on module.
     */
    public function unsubscribeFromModule(Request $request)
    {
        $request->validate([
            'module_id' => 'required|integer',
            'immediate' => 'nullable|boolean',
            'refund' => 'nullable|boolean',
            'reason' => 'nullable|string',
        ]);

        $brandId = $this->getBrandId($request);
        $data = RemoveModuleData::fromRequest($request);

        $result = $this->moduleService->unsubscribeFromModule($brandId, $data);

        return $this->apiSuccess($result, translate('message.unsubscription_success'));
    }

    /**
     * Preview proration for module add.
     */
    public function previewModuleProration(Request $request)
    {
        $request->validate([
            'module_id' => 'required|integer',
            'billing_cycle' => 'required|in:monthly,quarterly,semi_annually,annually,biennially',
        ]);

        $brandId = $this->getBrandId($request);
        $proration = $this->moduleService->previewProration(
            $brandId,
            $request->module_id,
            $request->billing_cycle
        );

        return $this->apiSuccess($proration?->toArray());
    }

    /**
     * Get invoice history.
     */
    public function invoices(Request $request)
    {
        $brandId = $this->getBrandId($request);
        $filters = $request->only(['status', 'from_date', 'to_date']);
        $perPage = $request->get('per_page', 20);

        $invoices = $this->billingService->getInvoiceHistory($brandId, $filters, $perPage);
        return $this->apiPaginated($invoices);
    }

    /**
     * Get invoice details.
     */
    public function invoiceDetails(Request $request, $id)
    {
        $brandId = $this->getBrandId($request);
        $invoice = \Modules\Subscription\Entities\SubscriptionInvoice::where('brand_id', $brandId)
            ->with(['items', 'payments', 'currency'])
            ->findOrFail($id);

        return $this->apiSuccess($invoice);
    }

    /**
     * Download invoice PDF.
     */
    public function downloadInvoice(Request $request, $id)
    {
        $brandId = $this->getBrandId($request);
        $invoice = \Modules\Subscription\Entities\SubscriptionInvoice::where('brand_id', $brandId)
            ->findOrFail($id);

        // TODO: Generate PDF and return download response

        return $this->apiSuccess(['message' => translate('message.pdf_not_implemented')]);
    }

    /**
     * Pay an invoice.
     */
    public function payInvoice(Request $request, $id)
    {
        $brandId = $this->getBrandId($request);
        $invoice = \Modules\Subscription\Entities\SubscriptionInvoice::where('brand_id', $brandId)
            ->where('status', '!=', 'paid')
            ->findOrFail($id);

        $result = $this->paymentService->chargeInvoice($invoice);

        if ($result['success']) {
            return $this->apiSuccess($result['payment'], translate('message.payment_successful'));
        } else {
            return $this->apiError($result['error'] ?? translate('message.payment_failed'), 400);
        }
    }

    /**
     * Get payment history.
     */
    public function payments(Request $request)
    {
        $brandId = $this->getBrandId($request);
        $filters = $request->only(['status', 'gateway']);
        $perPage = $request->get('per_page', 20);

        $payments = $this->billingService->getPaymentHistory($brandId, $filters, $perPage);
        return $this->apiPaginated($payments);
    }

    /**
     * Get payment method list.
     */
    public function paymentMethods(Request $request)
    {
        $brandId = $this->getBrandId($request);
        $methods = \Modules\Payment\Entities\CustomerPaymentMethod::where('brand_id', $brandId)
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->apiSuccess($methods);
    }

    /**
     * Create setup intent for adding payment method.
     */
    public function createSetupIntent(Request $request)
    {
        $request->validate([
            'gateway' => 'required|in:stripe,paypal',
        ]);

        $brandId = $this->getBrandId($request);
        $result = $this->paymentService->createSetupIntent($brandId, $request->gateway);

        return $this->apiSuccess($result);
    }

    /**
     * Attach payment method.
     */
    public function attachPaymentMethod(Request $request)
    {
        $request->validate([
            'gateway' => 'required|in:stripe,paypal',
            'payment_method_id' => 'required|string',
            'set_as_default' => 'nullable|boolean',
        ]);

        $brandId = $this->getBrandId($request);
        $method = $this->paymentService->attachPaymentMethod(
            $brandId,
            $request->gateway,
            $request->payment_method_id,
            $request->boolean('set_as_default', true)
        );

        return $this->apiSuccess($method, translate('message.action_completed'));
    }

    /**
     * Set default payment method.
     */
    public function setDefaultPaymentMethod(Request $request, $id)
    {
        $brandId = $this->getBrandId($request);
        $method = \Modules\Payment\Entities\CustomerPaymentMethod::where('brand_id', $brandId)
            ->findOrFail($id);

        // Update this as default
        $method->update(['is_default' => true]);

        // Unset others
        \Modules\Payment\Entities\CustomerPaymentMethod::where('brand_id', $brandId)
            ->where('id', '!=', $id)
            ->update(['is_default' => false]);

        // Update billing profile
        \Modules\Subscription\Entities\BrandBillingProfile::where('brand_id', $brandId)
            ->update(['default_payment_method_id' => $id]);

        return $this->apiSuccess($method, translate('message.updated_successfully'));
    }

    /**
     * Remove payment method.
     */
    public function removePaymentMethod(Request $request, $id)
    {
        $brandId = $this->getBrandId($request);
        $method = \Modules\Payment\Entities\CustomerPaymentMethod::where('brand_id', $brandId)
            ->findOrFail($id);

        if ($method->is_default) {
            return $this->apiError(translate('message.cannot_remove_default_payment'), 400);
        }

        $method->delete();

        return $this->apiSuccess(null, translate('message.payment_method_removed'));
    }

    /**
     * Retry a failed payment.
     */
    public function retryPayment(Request $request, $id)
    {
        $brandId = $this->getBrandId($request);
        $payment = \Modules\Subscription\Entities\SubscriptionPayment::where('brand_id', $brandId)
            ->where('status', 'failed')
            ->findOrFail($id);

        $result = $this->paymentService->retryPayment($payment->id);

        if ($result['success']) {
            return $this->apiSuccess($result['payment'], translate('message.payment_retry_success'));
        } else {
            return $this->apiError($result['error'] ?? translate('message.payment_retry_failed'), 400);
        }
    }

    /**
     * Get upcoming invoice preview.
     */
    public function upcomingInvoice(Request $request)
    {
        $brandId = $this->getBrandId($request);
        $subscription = \Modules\Subscription\Entities\PlanSubscription::where('brand_id', $brandId)
            ->whereIn('status', ['active', 'trial'])
            ->first();

        if (!$subscription) {
            return $this->apiSuccess(null, translate('message.no_active_subscription'));
        }

        $preview = $this->invoiceService->previewUpcomingInvoice($subscription);
        return $this->apiSuccess($preview);
    }

    /**
     * Get brand ID from request context.
     */
    private function getBrandId(Request $request): int
    {
        // Get from authenticated user's tenant context
        // This should be resolved by tenant middleware
        return $request->attributes->get('brand_id')
            ?? auth()->user()?->brand_id
            ?? throw new \InvalidArgumentException('Brand ID not found in request context');
    }
}
