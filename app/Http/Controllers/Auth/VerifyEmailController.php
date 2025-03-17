<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Responses\MessageResponse;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

final class VerifyEmailController
{
    public function __invoke(EmailVerificationRequest $request): MessageResponse
    {
        // Get user from TokenAuthGuard
        /** @var \App\Models\User $user */
        $user = $request->user();

        // If user has already verified email return message
        if ($user->hasVerifiedEmail()) {
            return new MessageResponse(
                message: __('auth.verified.already'),
            );
        }

        // Mark email as verified
        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        // Return success message
        return new MessageResponse(
            message: __('auth.verified.success'),
        );
    }
}
