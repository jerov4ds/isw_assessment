<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
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
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param \Throwable $e
     * @return JsonResponse
     */
    public function render($request, Throwable $e): JsonResponse
    {
        Log::error($e);
        if ($request->expectsJson() || $request->is('api/*')) {
            $code = 500;
            $status = 'error';
            $message = 'Oh snag! Something went wrong. We are fixing it';

            if ($e instanceof ModelNotFoundException) {
                $code = Response::HTTP_NOT_FOUND;
                $message = 'Resource not found';
            } elseif ($e instanceof NotFoundHttpException) {
                $code = Response::HTTP_NOT_FOUND;
                $message = 'Endpoint not found';
            } elseif ($e instanceof MethodNotAllowedHttpException) {
                $code = Response::HTTP_METHOD_NOT_ALLOWED;
                $message = 'Method not allowed';
            } elseif ($e instanceof AuthenticationException) {
                $code = Response::HTTP_UNAUTHORIZED;
                $message = 'Unauthenticated';
            } elseif ($e instanceof HttpException) {
                $code = $e->getStatusCode();
                $message = $e->getMessage() ?: 'HTTP error';
            }

            return response()->json([
                'code' => $code,
                'message' => $message,
                'status' => $status,
            ], $code);
        }

        return parent::render($request, $e);
    }
}
