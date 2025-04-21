<?php

declare(strict_types=1);

namespace App\Http\Controllers\Albums;

use App\Http\Responses\ApiResponse;
use App\Jobs\Albums\RestoreAlbumJob;
use App\Models\Album;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Facades\Gate;

final class RestoreAlbumController
{
    public function __construct(
        private readonly Dispatcher $dispatcher,
    ) {}

    public function __invoke(string $albumId): ApiResponse
    {
        // Get deleted album
        $album = Album::query()->where('id', $albumId)->onlyTrashed()->first();

        // Album not found, return error response
        if (null === $album) {
            return ApiResponse::notFound(
                message: __('albums.not_found'),
            );
        }

        // Not allowed by AlbumPolicy, return error response
        if ( ! Gate::allows('restore', $album)) {
            return ApiResponse::forbidden(
                message: __('albums.restore.failure'),
            );
        }

        // Restore album by dispatching a job
        $this->dispatcher->dispatch(
            command: new RestoreAlbumJob(
                album: $album,
            ),
        );

        // Return success response
        return ApiResponse::accepted(
            message: __('albums.restore.success'),
        );
    }
}
