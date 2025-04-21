<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Requests\Auth\NewPasswordRequest;
use App\Http\Responses\ApiResponse;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

final class PasswordResetByTokenController
{
    public function __invoke(NewPasswordRequest $request): ApiResponse
    {
        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse this as a validation exception.
        /** @var string|null */
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (\App\Models\User $user) use ($request): void {
                $user->forceFill([
                    'password' => Hash::make((string) $request->string('password')),
                ])->save();

                event(new PasswordReset($user));
            },
        );

        // Return error response
        if (Password::PASSWORD_RESET !== $status) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }

        // Return success response
        return ApiResponse::ok(
            message: __($status),
        );
    }
}
