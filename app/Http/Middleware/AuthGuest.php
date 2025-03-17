<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Http\Responses\ErrorResponse;
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
    public function handle(Request $request, Closure $next): ErrorResponse|Response
    {
        // If the user is authenticated, return a forbidden response
        if ( ! Auth::guest()) {
            return new ErrorResponse(
                message: __('auth.authenticated'),
                status: Response::HTTP_FORBIDDEN,
            );
        }

        return $next($request);
    }
}
