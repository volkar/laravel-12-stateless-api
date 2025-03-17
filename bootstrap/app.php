<?php

declare(strict_types=1);

use App\Http\Middleware\AuthGuest;
use App\Http\Middleware\AuthRequired;
use App\Http\Middleware\SetRequestedLocale;
use App\Http\Responses\ErrorResponse;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Treblle\SecurityHeaders\Http\Middleware\CertificateTransparencyPolicy;
use Treblle\SecurityHeaders\Http\Middleware\ContentTypeOptions;
use Treblle\SecurityHeaders\Http\Middleware\PermissionsPolicy;
use Treblle\SecurityHeaders\Http\Middleware\RemoveHeaders;
use Treblle\SecurityHeaders\Http\Middleware\SetReferrerPolicy;
use Treblle\SecurityHeaders\Http\Middleware\StrictTransportSecurity;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__ . '/../routes/api/routes.php',
        commands: __DIR__ . '/../routes/console/routes.php',
        apiPrefix: '',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->api(prepend: [
            RemoveHeaders::class,
            SetReferrerPolicy::class,
            StrictTransportSecurity::class,
            PermissionsPolicy::class,
            ContentTypeOptions::class,
            CertificateTransparencyPolicy::class,
            SetRequestedLocale::class,
        ]);
        $middleware->alias([
            'verified' => App\Http\Middleware\EnsureEmailIsVerified::class,
            'role' => App\Http\Middleware\Role::class,
            'auth.required' => AuthRequired::class,
            'auth.guest' => AuthGuest::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(fn(HttpException $e, Request $request): ErrorResponse => new ErrorResponse(
            message: $e->getMessage(),
            status: $e->getStatusCode(),
        ));
        $exceptions->shouldRenderJsonWhen(fn() => true);
    })->create();
