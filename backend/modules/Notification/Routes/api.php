<?php

use Illuminate\Support\Facades\Route;
use Modules\Notification\Http\Controllers\NotificationController;
use Modules\Notification\Http\Controllers\PushSubscriptionController;

// ─── Landlord Notifications ─────────────────────────────────────────
Route::prefix('landlord')->name('landlord.')->middleware(['auth:api', 'landlord_roles', 'throttle:60,1'])->group(function () {
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'list'])->name('index');
        Route::get('stats', [NotificationController::class, 'stats'])->name('stats');
        Route::get('unread-count', [NotificationController::class, 'unreadCount'])->name('unread-count');
        Route::post('{id}/read', [NotificationController::class, 'markAsRead'])->name('mark-read');
        Route::post('{id}/unread', [NotificationController::class, 'markAsUnread'])->name('mark-unread');
        Route::post('read-all', [NotificationController::class, 'markAllAsRead'])->name('read-all');
        Route::delete('{id}', [NotificationController::class, 'destroy'])->name('destroy');
        Route::get('preferences', [NotificationController::class, 'getPreferences'])->name('preferences');
        Route::put('preferences', [NotificationController::class, 'updatePreferences'])->name('preferences.update');

        Route::prefix('push')->name('push.')->group(function () {
            Route::post('subscribe', [PushSubscriptionController::class, 'subscribe'])->name('subscribe');
            Route::post('unsubscribe', [PushSubscriptionController::class, 'unsubscribe'])->name('unsubscribe');
            Route::get('status', [PushSubscriptionController::class, 'status'])->name('status');
        });
    });
});

// ─── Tenant Notifications ────────────────────────────────────────
Route::prefix('tenant')->name('tenant.')->middleware(['auth:api', 'tenant_roles', 'throttle:60,1'])->group(function () {
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'list'])->name('index');
        Route::get('stats', [NotificationController::class, 'stats'])->name('stats');
        Route::get('unread-count', [NotificationController::class, 'unreadCount'])->name('unread-count');
        Route::post('{id}/read', [NotificationController::class, 'markAsRead'])->name('mark-read');
        Route::post('{id}/unread', [NotificationController::class, 'markAsUnread'])->name('mark-unread');
        Route::post('read-all', [NotificationController::class, 'markAllAsRead'])->name('read-all');
        Route::delete('{id}', [NotificationController::class, 'destroy'])->name('destroy');
        Route::get('preferences', [NotificationController::class, 'getPreferences'])->name('preferences');
        Route::put('preferences', [NotificationController::class, 'updatePreferences'])->name('preferences.update');

        Route::prefix('push')->name('push.')->group(function () {
            Route::post('subscribe', [PushSubscriptionController::class, 'subscribe'])->name('subscribe');
            Route::post('unsubscribe', [PushSubscriptionController::class, 'unsubscribe'])->name('unsubscribe');
            Route::get('status', [PushSubscriptionController::class, 'status'])->name('status');
        });
    });
});
