<?php

declare(strict_types=1);

namespace App\Http\Controllers\Albums;

use App\Http\Responses\ErrorResponse;
use App\Http\Responses\MessageResponse;
use App\Jobs\Albums\RestoreAlbumJob;
use App\Models\Album;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

final class RestoreAlbumController
{
    public function __construct(
        private readonly Dispatcher $dispatcher,
    ) {}

    public function __invoke(string $albumId): ErrorResponse|MessageResponse
    {
        // Get deleted album
        $album = Album::query()->where('id', $albumId)->onlyTrashed()->first();

        // Album not found, return error response
        if (null === $album) {
            return new ErrorResponse(
                message: __('albums.not_found'),
                status: Response::HTTP_NOT_FOUND,
            );
        }

        // Not allowed by AlbumPolicy, return error response
        if ( ! Gate::allows('restore', $album)) {
            return new ErrorResponse(
                message: __('albums.restore.failure'),
                status: Response::HTTP_FORBIDDEN,
            );
        }

        // Restore album by dispatching a job
        $this->dispatcher->dispatch(
            command: new RestoreAlbumJob(
                album: $album,
            ),
        );

        // Return success response
        return new MessageResponse(
            message: __('albums.restore.success'),
            status: Response::HTTP_ACCEPTED,
        );
    }
}
