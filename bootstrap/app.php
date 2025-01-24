<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        using: function () {
            Route::middleware(['web', 'ip.blacklist'])->group(base_path('routes/web.php'));
            Route::middleware(['web', 'ip.blacklist'])->group(base_path('routes/landlord/web.php'));
            Route::middleware(['web', 'ip.blacklist'])->group(base_path('routes/modules.php'));
            Route::prefix('api')->middleware(['api', 'ip.blacklist'])->group(base_path('routes/api/modules.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            '2fa' => \App\Http\Middleware\Validate2FA::class,
            'set-db-connection' => \App\Http\Middleware\SetDatabaseConnection::class,
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'auth' => \App\Http\Middleware\Authenticate::class,
            'ip.blacklist' => \App\Http\Middleware\CheckIpBlacklist::class,
        ]);

        $middleware->group('tenant', [
            'set-db-connection',
            \Spatie\Multitenancy\Http\Middleware\NeedsTenant::class,
            \Spatie\Multitenancy\Http\Middleware\EnsureValidTenantSession::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {})
    ->withProviders([])
    ->withCommands([
        __DIR__ . '/../app/Console/Commands',
    ])
    ->create();
