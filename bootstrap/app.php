<?php

use App\Facades\ApiResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        $wantsJson = fn (Request $request): bool => $request->is('api/*') || $request->expectsJson();

        $exceptions->render(function (ValidationException $e, Request $request) use ($wantsJson) {
            if (! $wantsJson($request)) {
                return null;
            }

            return ApiResponse::validation($e->errors(), $e->getMessage() ?: 'Validation failed.');
        });

        $exceptions->render(function (AuthenticationException $e, Request $request) use ($wantsJson) {
            if (! $wantsJson($request)) {
                return null;
            }

            return ApiResponse::unauthorized($e->getMessage() ?: 'Unauthenticated.');
        });

        $exceptions->render(function (AuthorizationException $e, Request $request) use ($wantsJson) {
            if (! $wantsJson($request)) {
                return null;
            }

            return ApiResponse::forbidden($e->getMessage() ?: 'Forbidden.');
        });

        $exceptions->render(function (AccessDeniedHttpException $e, Request $request) use ($wantsJson) {
            if (! $wantsJson($request)) {
                return null;
            }

            return ApiResponse::forbidden($e->getMessage() ?: 'Forbidden.');
        });

        $exceptions->render(function (ModelNotFoundException $e, Request $request) use ($wantsJson) {
            if (! $wantsJson($request)) {
                return null;
            }

            return ApiResponse::notFound('Resource not found.');
        });

        $exceptions->render(function (NotFoundHttpException $e, Request $request) use ($wantsJson) {
            if (! $wantsJson($request)) {
                return null;
            }

            return ApiResponse::notFound('Resource not found.');
        });

        $exceptions->render(function (TooManyRequestsHttpException $e, Request $request) use ($wantsJson) {
            if (! $wantsJson($request)) {
                return null;
            }

            return ApiResponse::error('Too many requests. Please try again later.', 429);
        });

        $exceptions->render(function (Throwable $e, Request $request) use ($wantsJson) {
            if (! $wantsJson($request)) {
                return null;
            }

            if ($e instanceof HttpExceptionInterface) {
                $status = $e->getStatusCode();
                $message = $e->getMessage() ?: 'Request failed.';

                return ApiResponse::error($message, $status);
            }

            $message = config('app.debug')
                ? ($e->getMessage() ?: 'Server Error')
                : 'Server Error';

            return ApiResponse::error($message, 500);
        });
    })->create();
