<?php

use Illuminate\Support\Facades\Route;
use Modules\Comment\Http\Controllers\CommentController;

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
    Route::resource('comments', CommentController::class)->names('comments');
    
    // Comment-specific routes
    Route::prefix('comments')->name('comments.')->group(function () {
        Route::get('object/{object_id}/{object_model}', [CommentController::class, 'getObjectComments'])->name('object');
        Route::post('{parentId}/reply', [CommentController::class, 'addReply'])->name('reply');
        Route::post('{commentId}/reaction', [CommentController::class, 'addReaction'])->name('reaction.add');
        Route::delete('{commentId}/reaction', [CommentController::class, 'removeReaction'])->name('reaction.remove');
        Route::patch('{commentId}/seen', [CommentController::class, 'markAsSeen'])->name('seen');
        Route::get('unseen-count', [CommentController::class, 'getUnseenCount'])->name('unseen-count');
        Route::get('search', [CommentController::class, 'search'])->name('search');
        Route::get('stats', [CommentController::class, 'getStats'])->name('stats');
        Route::patch('bulk-seen', [CommentController::class, 'bulkMarkAsSeen'])->name('bulk-seen');
        Route::get('recent', [CommentController::class, 'getRecentComments'])->name('recent');
        Route::get('activity', [CommentController::class, 'getActivity'])->name('activity');
        Route::get('{commentId}/thread', [CommentController::class, 'getThread'])->name('thread');
    });
});

// Tenant routes (for tenant users)
Route::prefix('tenant')->name('tenant.')->middleware(['auth:web', 'tenant', '2fa'])->group(function () {
    Route::resource('comments', CommentController::class)->names('comments');
    
    // Comment-specific routes for tenant users
    Route::prefix('comments')->name('comments.')->group(function () {
        Route::get('object/{object_id}/{object_model}', [CommentController::class, 'getObjectComments'])->name('object');
        Route::post('{parentId}/reply', [CommentController::class, 'addReply'])->name('reply');
        Route::post('{commentId}/reaction', [CommentController::class, 'addReaction'])->name('reaction.add');
        Route::delete('{commentId}/reaction', [CommentController::class, 'removeReaction'])->name('reaction.remove');
        Route::patch('{commentId}/seen', [CommentController::class, 'markAsSeen'])->name('seen');
        Route::get('unseen-count', [CommentController::class, 'getUnseenCount'])->name('unseen-count');
        Route::get('search', [CommentController::class, 'search'])->name('search');
        Route::get('recent', [CommentController::class, 'getRecentComments'])->name('recent');
        Route::get('{commentId}/thread', [CommentController::class, 'getThread'])->name('thread');
    });
});
