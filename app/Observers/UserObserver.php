<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\User;
use App\Services\CacheService;

final class UserObserver
{
    public function created(User $user): void
    {
        // Invalidate possible null cache
        CacheService::forgetUserWithAlbumsBySlug($user->slug);
        CacheService::forgetUserById($user->id);
    }

    public function updated(User $user): void
    {
        if ($user->wasChanged('slug')) {
            /** @var string */
            $oldSlug = $user->getOriginal('slug');

            // Slug has changed, invalidate the old slug cache
            CacheService::forgetUserWithAlbumsBySlug($oldSlug);
            // Invalidate all slug-based album cache as well
            CacheService::forgetAllAlbumsBySlugs($user, $oldSlug);
        }

        CacheService::forgetUserWithAlbumsBySlug($user->slug);
        CacheService::forgetUserById($user->id);
    }

    public function deleted(User $user): void
    {
        // Delete all albums (and their caches)
        $user->deleteAllAlbums();

        // Delete access token (with cache)
        $user->deleteAuthToken();

        // Invalidate user cache
        CacheService::forgetUserWithAlbumsBySlug($user->slug);
        CacheService::forgetUserById($user->id);
    }

    public function restored(User $user): void
    {
        // Invalidate possible null cache
        CacheService::forgetUserWithAlbumsBySlug($user->slug);
        CacheService::forgetUserById($user->id);
    }

    public function forceDeleted(User $user): void {}

}
