<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
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
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            // Validation errors
            if ($exception instanceof \Illuminate\Validation\ValidationException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $exception->errors(),
                ], 422);
            }

            // Auth errors
            if ($exception instanceof \Illuminate\Auth\AuthenticationException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated',
                ], 401);
            }

            // HTTP exceptions (404, 403, etc)
            if ($exception instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                $status = $exception->getStatusCode();
                $message = $exception->getMessage() ?: ($status === 404 ? 'Not Found' : 'Error');
                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], $status);
            }

            // Conflict (409)
            if ($exception instanceof \Illuminate\Http\Exceptions\HttpResponseException && $exception->getResponse()->getStatusCode() === 409) {
                return response()->json([
                    'success' => false,
                    'message' => 'Conflict',
                ], 409);
            }

            // Fallback
            $status = 500;
            if ($exception instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface) {
                $status = $exception->getStatusCode();
            }
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage() ?: 'Server Error',
            ], $status);
        }
        return parent::render($request, $exception);
    }
}
