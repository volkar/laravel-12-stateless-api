<?php

declare(strict_types=1);

namespace App\Http\Controllers\Albums;

use App\Http\Requests\Albums\UpdateAlbumRequest;
use App\Http\Responses\ApiResponse;
use App\Jobs\Albums\UpdateAlbumJob;
use App\Models\Album;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Facades\Gate;

final class UpdateAlbumController
{
    public function __construct(
        private readonly Dispatcher $dispatcher,
    ) {}

    public function __invoke(UpdateAlbumRequest $request, string $albumId): ApiResponse
    {
        // Get album
        $album = Album::find($albumId);

        // Album not found, return error response
        if (null === $album) {
            return ApiResponse::notFound(
                message: __('albums.not_found'),
            );
        }

        // Not allowed by AlbumPolicy, return error response
        if ( ! Gate::allows('update', $album)) {
            return ApiResponse::forbidden(
                message: __('albums.update.failure'),
            );
        }

        // Update album by dispatching a job
        $this->dispatcher->dispatch(
            command: new UpdateAlbumJob(
                payload: $request->payload(),
                album: $album,
            ),
        );

        // Return success response
        return ApiResponse::accepted(
            message: __('albums.update.success'),
        );
    }
}
