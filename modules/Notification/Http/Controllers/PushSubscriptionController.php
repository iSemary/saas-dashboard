<?php

namespace Modules\Notification\Http\Controllers;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Modules\Notification\Services\PushSubscriptionService;

class PushSubscriptionController extends ApiController
{
    protected $service;

    public function __construct(PushSubscriptionService $service)
    {
        $this->service = $service;
    }

    /**
     * Subscribe to push notifications
     */
    public function subscribe(Request $request)
    {
        $request->validate([
            'endpoint' => 'required|string',
            'keys' => 'required|array',
            'keys.p256dh' => 'required|string',
            'keys.auth' => 'required|string',
        ]);

        try {
            $subscription = $this->service->subscribe(
                auth()->id(),
                $request->only(['endpoint', 'keys'])
            );

            return $this->return(200, translate('push_subscription_created'), [
                'subscription' => $subscription
            ]);
        } catch (\Exception $e) {
            return $this->return(500, translate('push_subscription_failed'), [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Unsubscribe from push notifications
     */
    public function unsubscribe(Request $request)
    {
        try {
            $this->service->unsubscribe(auth()->id());
            return $this->return(200, translate('push_subscription_removed'));
        } catch (\Exception $e) {
            return $this->return(500, translate('push_unsubscription_failed'), [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get current subscription status
     */
    public function status()
    {
        $subscription = $this->service->getSubscription(auth()->id());
        
        return $this->return(200, translate('subscription_status'), [
            'subscribed' => $subscription !== null,
            'subscription' => $subscription
        ]);
    }

    /**
     * Test push notification
     */
    public function test()
    {
        try {
            $this->service->sendTestNotification(auth()->id());
            return $this->return(200, translate('test_notification_sent'));
        } catch (\Exception $e) {
            return $this->return(500, translate('test_notification_failed'), [
                'error' => $e->getMessage()
            ]);
        }
    }
}
