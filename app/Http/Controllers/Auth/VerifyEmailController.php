<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Responses\ApiResponse;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

final class VerifyEmailController
{
    public function __invoke(EmailVerificationRequest $request): ApiResponse
    {
        // Get user from TokenAuthGuard
        /** @var \App\Models\User $user */
        $user = $request->user();

        // If user has already verified email return message
        if ($user->hasVerifiedEmail()) {
            return ApiResponse::ok(
                message: __('auth.verified.already'),
            );
        }

        // Mark email as verified
        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        // Return success message
        return ApiResponse::ok(
            message: __('auth.verified.success'),
        );
    }
}
