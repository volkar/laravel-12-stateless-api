<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserSelfResource;
use App\Http\Responses\DataResponse;
use App\Models\AuthToken;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

final class LoginController
{
    public function __invoke(LoginRequest $request): DataResponse
    {
        // Rate limiter and Auth::attempt is done in LoginRequest
        $request->authenticate();

        // Get user from TokenAuthGuard
        /** @var \App\Models\User */
        $user = Auth::user();

        // Find user's token
        $token = AuthToken::query()->where('user_id', $user->id)->first();

        // If no token found, create new one
        if ( ! $token) {
            $token = AuthToken::create([
                'user_id' => $user->id,
                'token' => AuthToken::generateToken(),
            ]);
        }

        // Return success response with token and user data
        return new DataResponse(
            data: [
                'token' => $token->generateTokenString(),
                'user' => new UserSelfResource($user),
            ],
            status: Response::HTTP_OK,
        );
    }
}
