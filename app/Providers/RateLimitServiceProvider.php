<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

final class RateLimitServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        RateLimiter::for(
            name: 'api-get',
            callback: static fn() => Limit::perMinute(
                maxAttempts: 100,
            )->response(fn() => RateLimitServiceProvider::tooManyAttemptsResponse(minutes: 1)),
        );

        RateLimiter::for(
            name: 'api-modify',
            callback: static fn() => Limit::perMinute(
                maxAttempts: 20,
            )->response(fn() => RateLimitServiceProvider::tooManyAttemptsResponse(minutes: 1)),
        );

        RateLimiter::for(
            name: 'auth-verify',
            callback: static fn() => Limit::perMinute(
                maxAttempts: 5,
            )->response(fn() => RateLimitServiceProvider::tooManyAttemptsResponse(minutes: 1)),
        );

        RateLimiter::for(
            name: 'auth-logout',
            callback: static fn() => Limit::perMinute(
                maxAttempts: 10,
            )->response(fn() => RateLimitServiceProvider::tooManyAttemptsResponse(minutes: 1)),
        );

        RateLimiter::for(
            name: 'auth-register',
            callback: static fn() => Limit::perMinutes(
                decayMinutes: 3,
                maxAttempts: 5,
            )->response(fn() => RateLimitServiceProvider::tooManyAttemptsResponse(minutes: 3)),
        );

        RateLimiter::for(
            name: 'auth-sendmail',
            callback: static fn() => Limit::perMinutes(
                decayMinutes: 3,
                maxAttempts: 3,
            )->response(fn() => RateLimitServiceProvider::tooManyAttemptsResponse(minutes: 3)),
        );
    }

    private static function tooManyAttemptsResponse(int $minutes): JsonResponse
    {
        return response()->json(
            [
                'error' => [
                    'message' => trans_choice('api.throttle.failure', $minutes, ['minutes' => $minutes]),
                    'status' => 429,
                ],
            ],
            429,
        );
    }
}
