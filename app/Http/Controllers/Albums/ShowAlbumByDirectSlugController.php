<?php

declare(strict_types=1);

namespace App\Http\Controllers\Albums;

use App\Http\Resources\AlbumFullResource;
use App\Http\Responses\ApiResponse;
use App\Models\User;
use App\Services\CacheService;
use Illuminate\Support\Facades\Auth;

final class ShowAlbumByDirectSlugController
{
    public function __invoke(string $directAccessSlug): ApiResponse
    {
        // Get album
        $cachedAlbum = CacheService::getAlbumDirect($directAccessSlug);

        // If album found, get user
        if ($cachedAlbum) {
            $authorUser = CacheService::getUserById($cachedAlbum->user_id);
        }

        // If no album found or no user found, return error response
        if (null === $cachedAlbum || null === $authorUser) {
            return ApiResponse::notFound(
                message: __('albums.not_found'),
            );
        }

        // Get authenticated user from TokenAuthGuard
        /** @var User|null */
        $viewerUser = Auth::user();

        // Filter atlas for authenticated user
        $cachedAlbum['atlas'] = $cachedAlbum->filterAtlasForUser($authorUser, $viewerUser);

        // Return success response
        return ApiResponse::ok(
            data: new AlbumFullResource(resource: $cachedAlbum),
        );
    }
}
