<?php

declare(strict_types=1);

namespace App\Enums;

enum AccessEnum: string
{
    case PRIVATE = 'private';
    case SHARED = 'shared';
    case PUBLIC = 'public';
}
