<?php

use Illuminate\Support\Facades\Route;
use Modules\Notification\Http\Controllers\NotificationController;
use Modules\Notification\Http\Controllers\PushSubscriptionController;

Route::middleware('auth:api')->group(function () {
    // Notification CRUD routes
    Route::get('notifications', [NotificationController::class, 'list'])->name('api.notifications.index');
    Route::get('notifications/stats', [NotificationController::class, 'stats'])->name('api.notifications.stats');
    Route::get('notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('api.notifications.unread-count');
    Route::post('notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('api.notifications.mark-read');
    Route::post('notifications/{id}/unread', [NotificationController::class, 'markAsUnread'])->name('api.notifications.mark-unread');
    Route::post('notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('api.notifications.read-all');
    Route::delete('notifications/{id}', [NotificationController::class, 'destroy'])->name('api.notifications.destroy');

    // Push notification routes
    Route::prefix('notifications/push')->group(function () {
        Route::post('subscribe', [PushSubscriptionController::class, 'subscribe'])->name('api.notifications.push.subscribe');
        Route::post('unsubscribe', [PushSubscriptionController::class, 'unsubscribe'])->name('api.notifications.push.unsubscribe');
        Route::get('status', [PushSubscriptionController::class, 'status'])->name('api.notifications.push.status');
    });

    // Notification preferences
    Route::get('notifications/preferences', [NotificationController::class, 'getPreferences'])->name('api.notifications.preferences');
    Route::put('notifications/preferences', [NotificationController::class, 'updatePreferences'])->name('api.notifications.preferences.update');
});