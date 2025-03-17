<?php

declare(strict_types=1);

namespace App\Enums;

enum AlbumItemTypeEnum: string
{
    case IMG = 'img';
    case TEXT = 'text';
    case TITLE = 'title';
}
