<?php

namespace App\Exceptions;

use App\Http\Controllers\ApiController;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Throwable;

class Handler extends ExceptionHandler {
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * The function returns an API response with a 400 status code and an error message, along with
     * additional debug information if the application is in debug mode.
     * 
     * @param $request
     * @param Throwable $exception
     * 
     * @return JsonResponse|\Illuminate\Http\Response
     */
    public function render($request, Throwable $exception) {
        // Always return JSON for AJAX requests or API routes
        if ($request->expectsJson() || $request->ajax() || $request->wantsJson() || str_starts_with($request->path(), 'api/')) {
            $statusCode = 500;
            if ($exception instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                $statusCode = $exception->getStatusCode();
            } elseif ($exception instanceof \Illuminate\Http\Exceptions\HttpResponseException) {
                $statusCode = $exception->getResponse()->getStatusCode();
            }
            return (new ApiController)->return($statusCode, env("APP_DEBUG") ? $exception->getMessage() : "Something went wrong!", [], env("APP_DEBUG") ?
                ['line' => $exception->getLine(), 'file' => $exception->getFile(), 'trace' => $exception->getTrace(),]
                : []);
        }
        
        // For non-AJAX requests, use parent handler (which returns HTML)
        return parent::render($request, $exception);
    }


    public function report(Throwable $exception) {
        if ($exception instanceof \League\OAuth2\Server\Exception\OAuthServerException && $exception->getCode() == 9) {
            return;
        }
        parent::report($exception);
    }

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void {
        $this->reportable(function (\League\OAuth2\Server\Exception\OAuthServerException $e) {
            if ($e->getCode() == 9)
                return false;
        });
    }
}
