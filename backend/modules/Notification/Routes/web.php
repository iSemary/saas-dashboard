<?php

use Illuminate\Support\Facades\Route;
use Modules\Notification\Http\Controllers\NotificationController;
use Modules\Notification\Http\Controllers\PushSubscriptionController;

Route::middleware(['auth:web', '2fa'])->group(function () {
    // Notification CRUD routes
    Route::resource('notifications', NotificationController::class)->names('notifications')->except(['create', 'edit', 'update', 'show']);

    // Notification management routes
    Route::get('notifications/list', [NotificationController::class, 'list'])->name('notifications.list');
    Route::get('notifications/stats', [NotificationController::class, 'stats'])->name('notifications.stats');
    
    Route::post('notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-as-read');
    Route::post('notifications/mark-as-read/{id}', [NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
    Route::post('notifications/mark-as-unread/{id}', [NotificationController::class, 'markAsUnread'])->name('notifications.mark-as-unread');

    // Push notification routes
    Route::prefix('notifications/push')->name('notifications.push.')->group(function () {
        Route::post('subscribe', [PushSubscriptionController::class, 'subscribe'])->name('subscribe');
        Route::post('unsubscribe', [PushSubscriptionController::class, 'unsubscribe'])->name('unsubscribe');
        Route::get('status', [PushSubscriptionController::class, 'status'])->name('status');
        Route::post('test', [PushSubscriptionController::class, 'test'])->name('test');
    });
});
