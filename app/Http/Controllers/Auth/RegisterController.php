<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Enums\RoleEnum;
use App\Enums\ThemeEnum;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserSelfResource;
use App\Http\Responses\DataResponse;
use App\Models\AuthToken;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

final class RegisterController
{
    public function __invoke(RegisterRequest $request): DataResponse
    {
        // Create user
        /** @var User */
        $user = User::query()->create([
            'name' => $request->name,
            'role' => RoleEnum::USER->value,
            'slug' => $request->slug,
            'theme' => ThemeEnum::LIGHT->value,
            'user_groups' => [],
            'email' => $request->email,
            'password' => Hash::make((string) $request->string('password')),
        ]);

        // Fire registered event
        event(new Registered($user));

        // Create token
        $token = AuthToken::create([
            'user_id' => $user->id,
            'token' => AuthToken::generateToken(),
        ]);

        // Return success response
        return new DataResponse(
            data: [
                'token' => $token->generateTokenString(),
                'user' => new UserSelfResource($user),
            ],
            status: Response::HTTP_OK,
        );
    }
}
