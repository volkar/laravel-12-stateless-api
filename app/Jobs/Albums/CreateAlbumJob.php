<?php

declare(strict_types=1);

namespace App\Jobs\Albums;

use App\Http\Payloads\Albums\CreateAlbumPayload;
use App\Models\Album;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\DatabaseManager;
use Illuminate\Foundation\Queue\Queueable;

final class CreateAlbumJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly CreateAlbumPayload $payload,
    ) {}

    public function handle(DatabaseManager $database): void
    {
        $database->transaction(
            callback: fn() => Album::query()->create(
                attributes: $this->payload->toArray(),
            ),
            attempts: 3,
        );
    }
}
