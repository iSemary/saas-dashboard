<?php

use Illuminate\Support\Facades\Route;
use Modules\Ticket\Http\Controllers\TicketController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::prefix('landlord')->name('landlord.')->middleware(['auth:web', 'role:landlord|developer', '2fa'])->group(function () {
    Route::resource('tickets', TicketController::class)->names('tickets');
    
    // Ticket-specific routes
    Route::prefix('tickets')->name('tickets.')->group(function () {
        Route::get('kanban', [TicketController::class, 'kanban'])->name('kanban');
        Route::get('kanban-data', [TicketController::class, 'getKanbanData'])->name('kanban-data');
        Route::patch('{id}/status', [TicketController::class, 'updateStatus'])->name('update-status');
        Route::patch('{id}/assign', [TicketController::class, 'assign'])->name('assign');
        Route::patch('{id}/close', [TicketController::class, 'close'])->name('close');
        Route::patch('{id}/reopen', [TicketController::class, 'reopen'])->name('reopen');
        Route::get('my-tickets', [TicketController::class, 'myTickets'])->name('my-tickets');
        Route::get('overdue', [TicketController::class, 'overdueTickets'])->name('overdue');
        Route::get('search', [TicketController::class, 'search'])->name('search');
        Route::get('stats', [TicketController::class, 'getStats'])->name('stats');
        Route::get('dashboard-data', [TicketController::class, 'getDashboardData'])->name('dashboard-data');
        Route::get('{id}/timeline', [TicketController::class, 'getTimeline'])->name('timeline');
        Route::patch('bulk-update', [TicketController::class, 'bulkUpdate'])->name('bulk-update');
        Route::get('metrics', [TicketController::class, 'getMetrics'])->name('metrics');
    });
});

// Tenant routes (for tenant users)
Route::prefix('tenant')->name('tenant.')->middleware(['auth:web', 'tenant', '2fa'])->group(function () {
    Route::resource('tickets', TicketController::class)->names('tickets');
    
    // Ticket-specific routes for tenant users
    Route::prefix('tickets')->name('tickets.')->group(function () {
        Route::get('kanban', [TicketController::class, 'kanban'])->name('kanban');
        Route::get('kanban-data', [TicketController::class, 'getKanbanData'])->name('kanban-data');
        Route::patch('{id}/status', [TicketController::class, 'updateStatus'])->name('update-status');
        Route::patch('{id}/assign', [TicketController::class, 'assign'])->name('assign');
        Route::patch('{id}/close', [TicketController::class, 'close'])->name('close');
        Route::patch('{id}/reopen', [TicketController::class, 'reopen'])->name('reopen');
        Route::get('my-tickets', [TicketController::class, 'myTickets'])->name('my-tickets');
        Route::get('overdue', [TicketController::class, 'overdueTickets'])->name('overdue');
        Route::get('search', [TicketController::class, 'search'])->name('search');
        Route::get('stats', [TicketController::class, 'getStats'])->name('stats');
        Route::get('{id}/timeline', [TicketController::class, 'getTimeline'])->name('timeline');
        Route::get('metrics', [TicketController::class, 'getMetrics'])->name('metrics');
    });
});
