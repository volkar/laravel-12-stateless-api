<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Album;
use App\Models\User;
use App\Services\CacheService;

final class AlbumObserver
{
    public function created(Album $album): void
    {
        /** @var User */
        $user = CacheService::getUserById($album->user_id);

        // Invalidate possible null cache
        CacheService::forgetAlbumBySlugs($user->slug, $album->slug);

        if ($album->direct_access_slug) {
            // Invalidate possible null cache
            CacheService::forgetAlbumDirect($album->direct_access_slug);
        }

        // Forget list of albums (list changed)
        CacheService::forgetUserWithAlbumsBySlug($user->slug);
    }

    public function updated(Album $album): void
    {
        /** @var User */
        $user = CacheService::getUserById($album->user_id);

        if ($album->wasChanged('slug')) {
            /** @var string */
            $oldSlug = $album->getOriginal('slug');

            // Forget current album by previous slugs (album changed)
            CacheService::forgetAlbumBySlugs($user->slug, $oldSlug);
        }

        // Forget current album by slugs (current or possible null)
        CacheService::forgetAlbumBySlugs($user->slug, $album->slug);

        if ($album->direct_access_slug) {
            // Forget current album by direct access (album changed)
            CacheService::forgetAlbumDirect($album->direct_access_slug);
        }

        // Forget list of albums (list changed)
        CacheService::forgetUserWithAlbumsBySlug($user->slug);
    }

    public function deleted(Album $album): void
    {
        /** @var User */
        $user = CacheService::getUserById($album->user_id);

        // Forget current album by slugs (album deleted)
        CacheService::forgetAlbumBySlugs($user->slug, $album->slug);

        if ($album->direct_access_slug) {
            // Forget current album by direct access slug (album deleted)
            CacheService::forgetAlbumDirect($album->direct_access_slug);
        }

        // Forget list of albums (list changed)
        CacheService::forgetUserWithAlbumsBySlug($user->slug);
    }

    public function restored(Album $album): void
    {
        /** @var User */
        $user = CacheService::getUserById($album->user_id);

        // Forget current album by slugs (album changed)
        CacheService::forgetAlbumBySlugs($user->slug, $album->slug);

        if ($album->direct_access_slug) {
            // Forget current album by direct access (album changed)
            CacheService::forgetAlbumDirect($album->direct_access_slug);
        }

        // Forget list of albums (list changed)
        CacheService::forgetUserWithAlbumsBySlug($user->slug);
    }

    public function forceDeleted(Album $album): void {}

}
