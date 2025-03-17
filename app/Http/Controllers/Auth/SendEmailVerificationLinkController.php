<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Responses\MessageResponse;
use Illuminate\Support\Facades\Auth;

final class SendEmailVerificationLinkController
{
    public function __invoke(): MessageResponse
    {
        // Get user from TokenAuthGuard
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // If user has already verified email return message
        if ($user->hasVerifiedEmail()) {
            return new MessageResponse(
                message: __('auth.verified.already'),
            );
        }

        // Send verification email
        $user->sendEmailVerificationNotification();

        // Return success message
        return new MessageResponse(
            message: __('auth.verified.sent'),
        );
    }
}
