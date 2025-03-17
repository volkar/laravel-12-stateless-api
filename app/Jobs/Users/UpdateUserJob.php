<?php

declare(strict_types=1);

namespace App\Jobs\Users;

use App\Http\Payloads\Users\UpdateUserPayload;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\DatabaseManager;
use Illuminate\Foundation\Queue\Queueable;

final class UpdateUserJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly UpdateUserPayload $payload,
        public readonly User $user,
    ) {}

    public function handle(DatabaseManager $database): void
    {
        $database->transaction(
            callback: fn() => $this->user->update(
                attributes: $this->payload->toArray(),
            ),
            attempts: 3,
        );
    }
}
