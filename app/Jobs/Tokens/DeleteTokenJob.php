<?php

declare(strict_types=1);

namespace App\Jobs\Tokens;

use App\Models\AuthToken;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\DatabaseManager;
use Illuminate\Foundation\Queue\Queueable;

final class DeleteTokenJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly AuthToken $token,
    ) {}

    public function handle(DatabaseManager $database): void
    {
        $database->transaction(
            callback: fn() => $this->token->delete(),
            attempts: 3,
        );
    }
}
