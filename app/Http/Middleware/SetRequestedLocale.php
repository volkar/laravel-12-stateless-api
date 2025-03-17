<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

final class SetRequestedLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Set locale based on Accept-Language header
        if ($request->header('Accept-Language') && in_array($request->header('Accept-Language'), ['en', 'ru'])) {
            App::setLocale($request->header('Accept-Language'));
        }

        return $next($request);
    }
}
