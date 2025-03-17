<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Responses\MessageResponse;
use Illuminate\Support\Facades\Auth;

final class LogoutController
{
    public function __invoke(): MessageResponse
    {
        // Get user from TokenAuthGuard
        /** @var \App\Models\User|null */
        $user = Auth::user();

        // Delete user's token
        if ($user) {
            $user->deleteAuthToken();
        }

        // Return success response
        return new MessageResponse(
            message: __('auth.logout.success'),
        );
    }
}
