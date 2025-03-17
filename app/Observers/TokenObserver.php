<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\AuthToken;
use App\Services\CacheService;

final class TokenObserver
{
    public function deleted(AuthToken $token): void
    {
        // Forget token cache
        CacheService::forgetTokenByTokenString($token->generateTokenString());
    }
}
