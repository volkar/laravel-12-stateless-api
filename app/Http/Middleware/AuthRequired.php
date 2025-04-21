<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Http\Responses\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

final class AuthRequired
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response|ApiResponse
    {
        // If user is authenticated, pass the request to the next middleware
        if (Auth::check()) {
            return $next($request);
        }

        // If user is not authenticated, return error response
        return ApiResponse::unauthorized(
            message: __('auth.unauthenticated'),
        );
    }
}
