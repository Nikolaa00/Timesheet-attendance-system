<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\UseSanctumTokenFromCookie;
use App\Exceptions\ApiException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        apiPrefix: '',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up'
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->api(prepend: [
            UseSanctumTokenFromCookie::class,
        ]);
        $middleware->alias([
            'role' => \App\Http\Middleware\EnsureRole::class,
            'active' => \App\Http\Middleware\IsActive::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        $formatError = function (array $loc, string $msg, string $type, mixed $input = null, array $ctx = [], int $status = 400) {
            return response()->json([
                'detail' => [
                    [
                        'loc' => $loc,
                        'msg' => $msg,
                        'type' => $type,
                        'input' => $input,
                        'ctx' => (object) $ctx,
                    ],
                ],
            ], $status);
        };

        $exceptions->render(function (ValidationException $e, $request) {
            $detail = [];

            foreach ($e->errors() as $field => $messages) {
                foreach ($messages as $message) {
                    $detail[] = [
                        'loc' => [$field],
                        'msg' => $message,
                        'type' => 'validation_error',
                        'input' => $request->input($field),
                        'ctx' => (object) [],
                    ];
                }
            }

            return response()->json(['detail' => $detail], 422);
        });

        $exceptions->render(function (ApiException $e, $request) {
            return $e->render($request);
        });

        $exceptions->render(function (AuthenticationException $e, $request) use ($formatError) {
            return $formatError(
                ['header'],
                'Unauthenticated.',
                'authentication_error',
                null,
                [],
                401
            );
        });

        $exceptions->render(function (AuthorizationException|AccessDeniedHttpException $e, $request) use ($formatError) {
            return $formatError(
                ['permission'],
                $e->getMessage() ?: 'This action is unauthorized.',
                'authorization_error',
                null,
                [],
                403
            );
        });

        $exceptions->render(function (NotFoundHttpException|ModelNotFoundException $e, $request) use ($formatError) {
            $previous = $e->getPrevious();

            if ($previous instanceof ModelNotFoundException) {
                $modelName = class_basename($previous->getModel());

                return $formatError(
                    ['id'],
                    "The requested {$modelName} was not found.",
                    'model_not_found_error',
                    null,
                    [],
                    404
                );
            }

            return $formatError(
                ['path'],
                'The requested URL or route was not found.',
                'route_not_found_error',
                null,
                [],
                404
            );
        });

        $exceptions->render(function (HttpExceptionInterface $e, $request) use ($formatError) {
            $status = $e->getStatusCode();

            $type = match ($status) {
                405 => 'method_not_allowed_error',
                503 => 'service_unavailable_error',
                default => 'http_error',
            };

            return $formatError(
                ['request'],
                $e->getMessage() ?: 'An HTTP error occurred.',
                $type,
                null,
                [],
                $status
            );
        });

        $exceptions->render(function (Throwable $e, $request) use ($formatError) {
            $isDebug = config('app.debug');

            $msg = $isDebug
                ? $e->getMessage()
                : 'An unexpected error occurred.';

            $ctx = $isDebug
                ? [
                    'exception' => get_class($e),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => array_slice($e->getTrace(), 0, 5),
                ]
                : [];

            return $formatError(['server'], $msg, 'internal_server_error', null, $ctx, 500);
        });

    })->create();