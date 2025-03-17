<?php

declare(strict_types=1);

namespace App\Http\Controllers\Albums;

use App\Http\Resources\AlbumFullResource;
use App\Http\Resources\UserResource;
use App\Http\Responses\DataResponse;
use App\Http\Responses\ErrorResponse;
use App\Models\User;
use App\Services\CacheService;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

final class ShowAlbumBySlugsController
{
    public function __invoke(string $userSlug, string $albumSlug): ErrorResponse|DataResponse
    {
        // Get album
        $album = CacheService::getAlbumBySlugs($userSlug, $albumSlug);

        // If album found, get user
        if (null !== $album) {
            $authorUser = CacheService::getUserById($album->user_id);
        }

        // Get authenticated user from TokenAuthGuard
        /** @var User|null */
        $viewerUser = Auth::user();

        // If no album found or no user found or album is not accessible, return error response
        if (null === $album || null === $authorUser || false === $album->isAlbumAccessible($authorUser, $viewerUser)) {
            return new ErrorResponse(
                message: __('albums.not_found'),
                status: Response::HTTP_NOT_FOUND,
            );
        }

        // Filter atlas for authenticated user
        $album['atlas'] = $album->filterAtlasForUser($authorUser, $viewerUser);

        // Return success response
        return new DataResponse(
            data: [
                'album' => new AlbumFullResource(resource: $album),
                'user' => new UserResource(resource: $authorUser),
            ],
        );
    }

}
