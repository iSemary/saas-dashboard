<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Tenant\Helper\TenantHelper;
use Spatie\Multitenancy\Models\Tenant;

class NotificationEvent implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels, Dispatchable;

    public $userId;
    public $data;
    public $tenantId;

    public function __construct($userId, $data)
    {
        $this->userId = $userId;
        $this->data = $data;

        $this->tenantId = Tenant::where("domain", TenantHelper::getSubDomain())->value("id");

        if ($this->tenantId) {
            Tenant::find($this->tenantId)?->makeCurrent();
        }

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
