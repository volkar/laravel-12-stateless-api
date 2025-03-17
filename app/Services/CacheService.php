<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\CacheKeyEnum;
use App\Models\Album;
use App\Models\AuthToken;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

final class CacheService
{
    /** @return mixed */
    public static function getCachedItem(string $key, callable $callback): mixed
    {
        // Time in seconds before invalidating cache of non-empty responses
        $cacheTtlForValidData = 60 * 60 * 24;
        // Time in seconds before invalidating cache of empty responses
        $cacheTtlForInvalidData = 60 * 60;

        /** @var array<mixed>|null */
        $cachedItem = Cache::get($key);

        if (null !== $cachedItem) {
            // Item exists in cache, return it (unwrap array)
            return $cachedItem[0];
        }

        // Item doesn't exist in cache, call callback
        $newItem = $callback();
        // If new item is not null, put it in cache for longer time
        $cacheTtl = null !== $newItem ? $cacheTtlForValidData : $cacheTtlForInvalidData;
        // Put new item in cache, with ttl (wrap in array)
        Cache::put($key, [$newItem], $cacheTtl);
        // Return new item
        return $newItem;
    }

    /** @return AuthToken|null */
    public static function getTokenByTokenString(string $tokenString): AuthToken|null
    {
        $result = self::getCachedItem(
            key: CacheKeyEnum::TOKEN_BY_STRING->value . '/' . $tokenString,
            callback: function () use ($tokenString): AuthToken|null {
                [$tokenId, $tokenStr] = explode(':', $tokenString);

                if ($tokenId && $tokenStr) {
                    /** @var ?AuthToken $token */
                    $token = AuthToken::find($tokenId);

                    if ($token && $token->token === $tokenStr) {
                        return $token;
                    }
                }
                return null;
            },
        );
        return ($result instanceof AuthToken) ? $result : null;
    }

    /** @return User|null */
    public static function getUserById(string $userId): User|null
    {
        $result = self::getCachedItem(
            key: CacheKeyEnum::USER_BY_ID->value . '/' . $userId,
            callback: fn() => User::find($userId),
        );
        return ($result instanceof User) ? $result : null;
    }

    /** @return array<string, User|Collection<int,Album>>|null */
    public static function getUserWithAlbumsBySlug(string $userSlug): array|null
    {
        /** @var array<string, User|Collection<int,Album>>|null */
        $result = self::getCachedItem(
            key: CacheKeyEnum::USER_WITH_ALBUMS->value . '/' . $userSlug,
            callback: function () use ($userSlug) {
                $user = User::query()->where('slug', $userSlug)->first();
                if ( ! $user) {
                    return null;
                }
                $albums = $user->albums()->orderBy('date_at', 'desc')->get();
                return ['user' => $user, 'albums' => $albums];
            },
        );
        if (is_array($result) && array_key_exists('user', $result) && array_key_exists('albums', $result)) {
            return $result;
        }
        return null;
    }

    /** @return Album|null */
    public static function getAlbumBySlugs(string $userSlug, string $albumSlug): Album|null
    {
        $result = self::getCachedItem(
            key: CacheKeyEnum::ALBUM_BY_SLUGS->value . '/' . $userSlug . '/' . $albumSlug,
            callback: fn() => Album::query()
                ->where('slug', $albumSlug)
                ->whereHas('user', function (Builder $query) use ($userSlug): void {
                    $query->where('slug', $userSlug);
                })
                ->first(),
        );
        return ($result instanceof Album) ? $result : null;
    }

    /** @return Album|null */
    public static function getAlbumDirect(string $directAccessSlug): Album|null
    {
        $result = self::getCachedItem(
            key: CacheKeyEnum::ALBUM_DIRECT->value . '/' . $directAccessSlug,
            callback: fn() => Album::query()->where('direct_access_slug', $directAccessSlug)->first(),
        );
        return ($result instanceof Album) ? $result : null;
    }

    public static function forgetTokenByTokenString(string $tokenString): void
    {
        Cache::forget(CacheKeyEnum::TOKEN_BY_STRING->value . '/' . $tokenString);
    }

    public static function forgetUserById(string $userId): void
    {
        Cache::forget(CacheKeyEnum::USER_BY_ID->value . '/' . $userId);
    }

    public static function forgetUserWithAlbumsBySlug(string $userSlug): void
    {
        Cache::forget(CacheKeyEnum::USER_WITH_ALBUMS->value . '/' . $userSlug);
    }

    public static function forgetAlbumBySlugs(string $userSlug, string $albumSlug): void
    {
        Cache::forget(CacheKeyEnum::ALBUM_BY_SLUGS->value . '/' . $userSlug . '/' . $albumSlug);
    }

    public static function forgetAlbumDirect(string $directAccessSlug): void
    {
        Cache::forget(CacheKeyEnum::ALBUM_DIRECT->value . '/' . $directAccessSlug);
    }

    public static function forgetAllAlbumsBySlugs(User $user, string $userSlug): void
    {
        $albums = $user->albums()->get();
        foreach ($albums as $album) {
            Cache::forget(CacheKeyEnum::ALBUM_BY_SLUGS->value . '/' . $userSlug . '/' . $album->slug);
        }
    }
}
