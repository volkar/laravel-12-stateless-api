<?php

declare(strict_types=1);

namespace App\Http\Controllers\Users;

use App\Http\Resources\AlbumForListResource;
use App\Http\Resources\UserResource;
use App\Http\Responses\ApiResponse;
use App\Models\Album;
use App\Models\User;
use App\Services\CacheService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

final class ShowUserController
{
    public function __invoke(string $userSlug): ApiResponse
    {
        // Get user with albums
        $userAndAlbums = CacheService::getUserWithAlbumsBySlug($userSlug);

        // If no user found, return error response
        if (null === $userAndAlbums || ! array_key_exists('user', $userAndAlbums) || ! array_key_exists('albums', $userAndAlbums)) {
            return ApiResponse::notFound(
                message: __('users.not_found'),
            );
        }

        /** @var User */
        $authorUser = $userAndAlbums['user'];

        /** @var Collection<int, Album> */
        $albums = $userAndAlbums['albums'];

        // Get authenticated user
        /** @var User|null */
        $viewerUser = Auth::user();

        /** @var array<int, mixed> */
        $filteredAlbums = [];

        // Filter cached albums by permissions
        $albums->each(function (Album $album) use (&$filteredAlbums, $authorUser, $viewerUser): void {
            if ($album->isAlbumAccessible($authorUser, $viewerUser)) {
                // Album is public or accessible for this user

                // Filter atlas
                $album['atlas'] = $album->filterAtlasForUser($authorUser, $viewerUser);

                // Add filtered album to response
                $filteredAlbums[] = new AlbumForListResource(
                    resource: $album,
                );
            }
        });

        // Return user and accessible albums
        return ApiResponse::ok(
            data: ['user' => new UserResource(resource: $authorUser), 'albums' => $filteredAlbums],
        );
    }

}
