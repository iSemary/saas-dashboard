<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Comment\Http\Controllers\CommentController;

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
    Route::apiResource('comments', CommentController::class);
    
    // Comment-specific API routes
    Route::prefix('comments')->group(function () {
        Route::get('object/{object_id}/{object_model}', [CommentController::class, 'getObjectComments']);
        Route::post('{parentId}/reply', [CommentController::class, 'addReply']);
        Route::post('{commentId}/reaction', [CommentController::class, 'addReaction']);
        Route::delete('{commentId}/reaction', [CommentController::class, 'removeReaction']);
        Route::patch('{commentId}/seen', [CommentController::class, 'markAsSeen']);
        Route::get('unseen-count', [CommentController::class, 'getUnseenCount']);
        Route::get('search', [CommentController::class, 'search']);
        Route::get('stats', [CommentController::class, 'getStats']);
        Route::patch('bulk-seen', [CommentController::class, 'bulkMarkAsSeen']);
        Route::get('recent', [CommentController::class, 'getRecentComments']);
        Route::get('activity', [CommentController::class, 'getActivity']);
        Route::get('{commentId}/thread', [CommentController::class, 'getThread']);
    });
});
