<?php

declare(strict_types=1);

namespace App\Enums;

enum RoleEnum: string
{
    case USER = 'user';
    case ADMIN = 'admin';
}
