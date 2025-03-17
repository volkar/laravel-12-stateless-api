<?php

declare(strict_types=1);

namespace App\Auth;

use App\Services\CacheService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;

final class TokenAuthGuard implements Guard
{
    /** @var Authenticatable|null */
    private $user;
    /** @var UserProvider */
    private $provider;

    public function __construct(UserProvider $provider)
    {
        $user = null;

        // Check if the user is authenticated by bearer token
        if (request()->bearerToken()) {
            $token = CacheService::getTokenByTokenString(request()->bearerToken());

            if ($token) {
                // If the token is found and valid, retrieve the user
                $user = CacheService::getUserById($token->user_id);
            }
        }

        // Assign the user and provider
        $this->provider = $provider;
        $this->user = $user;
    }

    /**
     * Determine if the current user is authenticated. If not, throw an exception.
     */
    public function authenticate(): Authenticatable
    {
        return $this->user() ?? throw new AuthenticationException();
    }

    /**
     * Determine if the current user is authenticated.
     */
    public function check(): bool
    {
        return null !== $this->user();
    }

    /**
     * Determine if the current user is a guest.
     */
    public function guest(): bool
    {
        return ! $this->check();
    }

    /**
     * Get the currently authenticated user.
     */
    public function user(): ?Authenticatable
    {
        return $this->user;
    }

    /**
     * Validate a user's credentials.
     *
     * @param  array<mixed>  $credentials
     */
    public function validate(array $credentials = []): bool
    {
        if (empty($credentials['email']) || empty($credentials['password'])) {
            return false;
        }

        $user = $this->provider->retrieveByCredentials($credentials);

        return null !== $user && $this->provider->validateCredentials($user, $credentials);
    }

    /**
     * Attempt to authenticate a user using the given credentials.
     *
     * @param  array<mixed>  $credentials
     */
    public function attempt(array $credentials = []): bool
    {
        if (empty($credentials['email']) || empty($credentials['password'])) {
            return false;
        }

        $user = $this->provider->retrieveByCredentials($credentials);

        if (null !== $user && $this->provider->validateCredentials($user, $credentials)) {

            $this->setUser($user);

            return true;
        }

        return false;
    }

    /**
     * Get the ID for the currently authenticated user.
     */
    public function id(): ?string
    {
        if ($this->check()) {
            /** @var Authenticatable */
            $user = $this->user();
            $id = $user->getAuthIdentifier();

            if (is_string($id)) {
                return $id;
            }
        }

        return null;
    }

    /**
     * Determine if the current user is authenticated.
     */
    public function hasUser(): bool
    {
        return null !== $this->user();
    }

    /**
     * Set the current user.
     */
    public function setUser(Authenticatable $user): static
    {
        $this->user = $user;

        return $this;
    }
}
