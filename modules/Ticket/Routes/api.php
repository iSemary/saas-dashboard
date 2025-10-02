<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Ticket\Http\Controllers\TicketController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->prefix('v1')->group(function () {
    Route::apiResource('tickets', TicketController::class);
    
    // Ticket-specific API routes
    Route::prefix('tickets')->group(function () {
        Route::get('kanban-data', [TicketController::class, 'getKanbanData']);
        Route::patch('{id}/status', [TicketController::class, 'updateStatus']);
        Route::patch('{id}/assign', [TicketController::class, 'assign']);
        Route::patch('{id}/close', [TicketController::class, 'close']);
        Route::patch('{id}/reopen', [TicketController::class, 'reopen']);
        Route::get('my-tickets', [TicketController::class, 'myTickets']);
        Route::get('overdue', [TicketController::class, 'overdueTickets']);
        Route::get('search', [TicketController::class, 'search']);
        Route::get('stats', [TicketController::class, 'getStats']);
        Route::get('dashboard-data', [TicketController::class, 'getDashboardData']);
        Route::get('{id}/timeline', [TicketController::class, 'getTimeline']);
        Route::patch('bulk-update', [TicketController::class, 'bulkUpdate']);
        Route::get('metrics', [TicketController::class, 'getMetrics']);
    });
});