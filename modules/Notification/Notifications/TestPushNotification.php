<?php

namespace Modules\Notification\Notifications;

class TestPushNotification extends BaseNotification
{
    public function __construct()
    {
        parent::__construct(
            'Test Push Notification',
            'This is a test push notification to verify your subscription is working correctly.',
            ['test' => true],
            'info',
            'low',
            '/favicon.ico'
        );
    }
}
