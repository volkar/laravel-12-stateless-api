<?php

declare(strict_types=1);

namespace App\Enums;

enum CacheKeyEnum: string
{
    case TOKEN_BY_STRING = 'token_by_string';

    case USER_BY_ID = 'user_by_id';
    case USER_WITH_ALBUMS = 'user_with_albums';

    case ALBUM_BY_SLUGS = 'album_by_slugs';
    case ALBUM_DIRECT = 'album_direct';
}
