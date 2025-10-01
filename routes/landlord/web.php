<?php

use App\Events\NotificationEvent;
use Modules\Customer\Http\Controllers\BrandController;
use Modules\Tenant\Http\Controllers\TenantOwnerController;
use App\Http\Controllers\Landlord\DocumentationController;
use Illuminate\Support\Facades\Route;

Route::prefix('landlord')->name('landlord.')->middleware(['auth:web', 'landlord_roles', '2fa'])->group(function () {
    Route::get("test/event", function () {
        broadcast(new NotificationEvent(auth()->user()->id, ["title" => "Hello From Laravel", "message" => "Hello From Laravel New Event From Test"]));
    });

    // Documentation routes
    Route::middleware('can:read.documentation')->group(function () {
        Route::get('documentation', [DocumentationController::class, 'index'])->name('documentation.index');
        Route::get('documentation/{file}', [DocumentationController::class, 'show'])->name('documentation.show');
        Route::post('documentation/get-content', [DocumentationController::class, 'getContent'])->name('documentation.get-content');
        Route::get('documentation/tree/files', [DocumentationController::class, 'getFileTree'])->name('documentation.tree');
    });

    // Brand routes
    Route::middleware('can:read.brands')->group(function () {
        Route::apiResource('brands', BrandController::class);
        Route::get('brands/slug/{slug}', [BrandController::class, 'showBySlug'])->name('brands.show-by-slug');
        Route::get('brands/tenant/{tenantId}', [BrandController::class, 'getByTenant'])->name('brands.by-tenant');
        Route::get('brands/search', [BrandController::class, 'search'])->name('brands.search');
        Route::post('brands/{id}/restore', [BrandController::class, 'restore'])->name('brands.restore');
        Route::get('brands/stats', [BrandController::class, 'stats'])->name('brands.stats');
    });

    // Tenant Owner routes
    Route::middleware('can:read.tenant_owners')->group(function () {
        Route::apiResource('tenant-owners', TenantOwnerController::class);
        Route::get('tenant-owners/tenant/{tenantId}', [TenantOwnerController::class, 'getByTenant'])->name('tenant-owners.by-tenant');
        Route::get('tenant-owners/tenant/{tenantId}/super-admins', [TenantOwnerController::class, 'getSuperAdmins'])->name('tenant-owners.super-admins');
        Route::get('tenant-owners/search', [TenantOwnerController::class, 'search'])->name('tenant-owners.search');
        Route::post('tenant-owners/{id}/restore', [TenantOwnerController::class, 'restore'])->name('tenant-owners.restore');
        Route::post('tenant-owners/{id}/promote', [TenantOwnerController::class, 'promoteToSuperAdmin'])->name('tenant-owners.promote');
        Route::post('tenant-owners/{id}/demote', [TenantOwnerController::class, 'demoteFromSuperAdmin'])->name('tenant-owners.demote');
        Route::put('tenant-owners/{id}/status', [TenantOwnerController::class, 'updateStatus'])->name('tenant-owners.update-status');
        Route::put('tenant-owners/{id}/permissions', [TenantOwnerController::class, 'updatePermissions'])->name('tenant-owners.update-permissions');
        Route::get('tenant-owners/stats', [TenantOwnerController::class, 'stats'])->name('tenant-owners.stats');
    });
});
