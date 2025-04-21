<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Http\Responses\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

final class AuthGuest
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): ApiResponse|Response
    {
        // If the user is authenticated, return a forbidden response
        if ( ! Auth::guest()) {
            return ApiResponse::forbidden(
                message: __('auth.authenticated'),
            );
        }

        return $next($request);
    }
}
