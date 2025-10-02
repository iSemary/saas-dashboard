<?php

return [
    /*
     * These are the keys for authentication (VAPID).
     * These keys must be safely stored and should not change.
     */
    'vapid' => [
        'subject' => env('VAPID_SUBJECT'),
        'public_key' => env('VAPID_PUBLIC_KEY'),
        'private_key' => env('VAPID_PRIVATE_KEY'),
    ],

    /*
     * This is the model that will be used to for push subscriptions.
     */
    'model' => \Modules\Notification\Entities\NotificationChannel::class,

    /*
     * This is the name of the table that will be created by the migration and
     * used by the above model. This can be changed if necessary.
     */
    'table_name' => 'notification_channels',

    /*
     * You can change the column names used by the model if necessary.
     */
    'columns' => [
        'user_id' => 'user_id',
        'endpoint' => 'subscription_data->endpoint',
        'public_key' => 'subscription_data->keys->p256dh',
        'auth_token' => 'subscription_data->keys->auth',
        'content_encoding' => null,
    ],

    /*
     * Default TTL for push notifications.
     */
    'ttl' => 2419200, // 4 weeks

    /*
     * Default urgency for push notifications. It can be very-low, low, normal, or high.
     */
    'urgency' => 'normal',

    /*
     * Default topic for push notifications.
     */
    'topic' => null,
];
