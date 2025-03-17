<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Http\Responses\ErrorResponse;
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
    public function handle(Request $request, Closure $next): Response|ErrorResponse
    {
        // If user is authenticated, pass the request to the next middleware
        if (Auth::check()) {
            return $next($request);
        }

        // If user is not authenticated, return error response
        return new ErrorResponse(
            message: __('auth.unauthenticated'),
            status: Response::HTTP_UNAUTHORIZED,
        );
    }
}
