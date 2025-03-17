<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Http\Responses\ErrorResponse;
use Closure;
use Illuminate\Http\Request;

final class Role
{
    public function handle(Request $request, Closure $next, string $role): mixed
    {
        /** @var \App\Models\User */
        $user = $request->user();

        // Check user role
        if ($user->role !== $role) {
            return new ErrorResponse(
                message: __('api.role.failure'),
                status: 403,
            );
        }

        // Pass the request to the next middleware
        return $next($request);
    }
}
