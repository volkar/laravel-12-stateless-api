<?php

declare(strict_types=1);

namespace App\Http\Controllers\Albums;

use App\Http\Resources\AlbumFullResource;
use App\Http\Responses\DataResponse;
use App\Http\Responses\ErrorResponse;
use App\Models\User;
use App\Services\CacheService;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

final class ShowAlbumByDirectSlugController
{
    public function __invoke(string $directAccessSlug): ErrorResponse|DataResponse
    {
        // Get album
        $cachedAlbum = CacheService::getAlbumDirect($directAccessSlug);

        // If album found, get user
        if ($cachedAlbum) {
            $authorUser = CacheService::getUserById($cachedAlbum->user_id);
        }

        // If no album found or no user found, return error response
        if (null === $cachedAlbum || null === $authorUser) {
            return new ErrorResponse(
                message: __('albums.not_found'),
                status: Response::HTTP_NOT_FOUND,
            );
        }

        // Get authenticated user from TokenAuthGuard
        /** @var User|null */
        $viewerUser = Auth::user();

        // Filter atlas for authenticated user
        $cachedAlbum['atlas'] = $cachedAlbum->filterAtlasForUser($authorUser, $viewerUser);

        // Return success response
        return new DataResponse(
            data: new AlbumFullResource(
                resource: $cachedAlbum,
            ),
        );
    }
}
