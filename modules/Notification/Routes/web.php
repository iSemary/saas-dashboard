<?php

use Illuminate\Support\Facades\Route;
use Modules\Notification\Http\Controllers\NotificationController;


Route::middleware(['auth:web', '2fa'])->group(function () {
    Route::resource('notifications', NotificationController::class)->names('notifications')->except(['create', 'edit', 'update', 'show']);

    Route::get('notifications/list', [NotificationController::class, 'list'])->name('notifications.list');
    
    Route::post('notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-as-read');
    Route::post('notifications/mark-as-read/{id}', [NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
    Route::post('notifications/mark-as-unread/{id}', [NotificationController::class, 'markAsUnread'])->name('notifications.mark-as-unread');
});
