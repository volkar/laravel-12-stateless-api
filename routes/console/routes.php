<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schedule;

Schedule::command(
    command: 'model:prune',
)->dailyAt('03:15')->withoutOverlapping()->onOneServer();
