<?php

declare(strict_types=1);

namespace App\Http\Controllers\Albums;

use App\Http\Responses\ErrorResponse;
use App\Http\Responses\MessageResponse;
use App\Jobs\Albums\DeleteAlbumJob;
use App\Models\Album;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

final class DeleteAlbumController
{
    public function __construct(
        private readonly Dispatcher $dispatcher,
    ) {}

    public function __invoke(string $albumId): ErrorResponse|MessageResponse
    {
        // Get album
        $album = Album::find($albumId);

        // Album not found, return error response
        if (null === $album) {
            return new ErrorResponse(
                message: __('albums.not_found'),
                status: Response::HTTP_NOT_FOUND,
            );
        }

        // Not allowed by AlbumPolicy, return error response
        if ( ! Gate::allows('delete', $album)) {
            return new ErrorResponse(
                message: __('albums.delete.failure'),
                status: Response::HTTP_FORBIDDEN,
            );
        }

        // Delete album by dispatching a job
        $this->dispatcher->dispatch(
            command: new DeleteAlbumJob(
                album: $album,
            ),
        );

        // Return success response
        return new MessageResponse(
            message: __('albums.delete.success'),
            status: Response::HTTP_ACCEPTED,
        );
    }
}
