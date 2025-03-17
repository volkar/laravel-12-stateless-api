<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\Cache\ClearCacheJob;
use Illuminate\Console\Command;
use Illuminate\Contracts\Bus\Dispatcher;

use function Laravel\Prompts\info;

use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'clearcache', description: 'Clear all cache')]
final class ClearCacheCommand extends Command
{
    public function handle(Dispatcher $dispatcher): void
    {
        info(
            message: 'Clearing cache...',
        );

        $dispatcher->dispatchNow(
            command: new ClearCacheJob(),
        );
    }
}
