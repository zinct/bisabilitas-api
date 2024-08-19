<?php

use App\Traits\ApiTrait;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->statefulApi();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->renderable(function (Exception $e, $request) {
            if ($request->wantsJson()) {
                if ($e instanceof ValidationException) return (new class
                {
                    use ApiTrait;
                })->sendResponse($e->getMessage(), data: $e->errors(), code: 422);

                if ($e instanceof AuthenticationException) return (new class
                {
                    use ApiTrait;
                })->sendResponse($e->getMessage(), code: 401);


                if ($e instanceof NotFoundHttpException) return (new class
                {
                    use ApiTrait;
                })->sendResponse($e->getMessage(), code: 404);

                return (new class
                {
                    use ApiTrait;
                })->sendResponse($e->getMessage(), code: 500);
            }
        });
    })->create();
