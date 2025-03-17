<?php

declare(strict_types=1);

namespace App\Jobs\Albums;

use App\Models\Album;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\DatabaseManager;
use Illuminate\Foundation\Queue\Queueable;

final class DeleteAlbumJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Album $album,
    ) {}

    public function handle(DatabaseManager $database): void
    {
        $database->transaction(
            callback: fn() => $this->album->delete(),
            attempts: 3,
        );
    }
}
