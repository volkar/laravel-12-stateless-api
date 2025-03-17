<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Requests\Auth\PasswordResetLinkRequest;
use App\Http\Responses\MessageResponse;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

final class SendPasswordResetLinkController
{
    public function __invoke(PasswordResetLinkRequest $request): MessageResponse
    {
        // Send password reset link
        $status = Password::sendResetLink(
            $request->only('email'),
        );

        // Return error response
        if (Password::RESET_LINK_SENT !== $status) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }

        // Return success response
        return new MessageResponse(
            message: __($status),
        );
    }
}
