<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path and query parameters the user should be redirected to when they are not authenticated.
     *
     * @param \Illuminate\Http\Request $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (!$request->expectsJson()) {
            $redirectUrl = $request->query('redirect', urlencode($request->getRequestUri()));
            return route('login', ['redirect' => $redirectUrl]);
        }
    }

    /**
     * Handle unauthenticated users.
     *
     * @param \Illuminate\Http\Request $request
     * @param array $guards
     * @return void
     */
    protected function unauthenticated($request, array $guards)
    {
        if ($request->expectsJson()) {
            throw new \Illuminate\Auth\AuthenticationException;
        }

        // The redirectTo method will handle the redirect properly
        throw new \Illuminate\Auth\AuthenticationException(
            'Unauthenticated.', $guards, $this->redirectTo($request)
        );
    }
}
