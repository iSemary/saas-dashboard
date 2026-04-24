<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Modules\Tenant\Helper\TenantHelper;
use Spatie\Multitenancy\Models\Tenant;

class TestEvent implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels, Dispatchable;

    public $userId;
    public $data;
    public $tenantId;

    public function __construct($userId, $data)
    {
        $this->userId = $userId;
        $this->data = $data;

        $this->tenantId = Tenant::where("domain", TenantHelper::getSubDomain())->first()?->id; // Store current tenant
        Tenant::find($this->tenantId)?->makeCurrent();
        Log::info("Broadcasting for tenant: {$this->tenantId}");
        $this->broadcastOn();
    }

    public function broadcastOn()
    {
        return new PrivateChannel("user.notification.{$this->userId}");
    }

    public function broadcastWith(): array
    {
        return array(
            'data' => $this->data
        );
    }

    public function broadcastAs()
    {
        return "user.notification";
    }
}
