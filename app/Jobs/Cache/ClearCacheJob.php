<?php

declare(strict_types=1);

namespace App\Jobs\Cache;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;

use function Laravel\Prompts\info;

final class ClearCacheJob implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        Cache::flush();

        info(
            message: 'Cache cleared.',
        );
    }
}
