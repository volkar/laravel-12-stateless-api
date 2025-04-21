<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Responses\ApiResponse;
use Illuminate\Support\Facades\Auth;

final class LogoutController
{
    public function __invoke(): ApiResponse
    {
        // Get user from TokenAuthGuard
        /** @var \App\Models\User|null */
        $user = Auth::user();

        // Delete user's token
        if ($user) {
            $user->deleteAuthToken();
        }

        // Return success response
        return ApiResponse::ok(
            message: __('auth.logout.success'),
        );
    }
}
