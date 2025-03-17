<?php

declare(strict_types=1);

namespace App\Auth;

use App\Models\User;
use App\Services\CacheService;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Support\Facades\Hash;
use SensitiveParameter;

final class TokenAuthProvider implements UserProvider
{
    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  string  $identifier
     */
    public function retrieveById($identifier)
    {
        return CacheService::getUserById($identifier);
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array<mixed>  $credentials
     */
    public function retrieveByCredentials(#[SensitiveParameter] array $credentials)
    {
        if (empty($credentials['email']) || ! is_string($credentials['email'])) {
            return null;
        }

        return User::query()->where('email', $credentials['email'])->first();
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param  array<mixed>  $credentials
     */
    public function validateCredentials(UserContract $user, #[SensitiveParameter] array $credentials)
    {
        if (null === ($plain = $credentials['password']) || ! is_string($plain)) {
            return false;
        }

        if ( ! ($hashed = $user->getAuthPassword())) {
            return false;
        }

        return Hash::check($plain, $hashed);
    }

    /**
     * Retrieve a user by their unique identifier and "remember me" token.
     * Not used (needed by interface)
     */
    public function retrieveByToken($identifier, #[SensitiveParameter] $token)
    {
        return null;
    }

    /**
     * Update the "remember me" token for the given user in storage.
     * Not used (needed by interface)
     */
    public function updateRememberToken(UserContract $user, #[SensitiveParameter] $token): void {}

    /**
     * Rehash the user's password if required and supported.
     * Not used (needed by interface)
     *
     * @param  User  $user
     * @param  array<mixed>  $credentials
     */
    public function rehashPasswordIfRequired(UserContract $user, #[SensitiveParameter] array $credentials, bool $force = false): void {}
}
