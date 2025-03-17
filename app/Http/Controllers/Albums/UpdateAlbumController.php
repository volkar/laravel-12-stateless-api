<?php

declare(strict_types=1);

namespace App\Http\Controllers\Albums;

use App\Http\Requests\Albums\UpdateAlbumRequest;
use App\Http\Responses\ErrorResponse;
use App\Http\Responses\MessageResponse;
use App\Jobs\Albums\UpdateAlbumJob;
use App\Models\Album;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

final class UpdateAlbumController
{
    public function __construct(
        private readonly Dispatcher $dispatcher,
    ) {}

    public function __invoke(UpdateAlbumRequest $request, string $albumId): ErrorResponse|MessageResponse
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
        if ( ! Gate::allows('update', $album)) {
            return new ErrorResponse(
                message: __('albums.update.failure'),
                status: Response::HTTP_FORBIDDEN,
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
        return new MessageResponse(
            message: __('albums.update.success'),
            status: Response::HTTP_ACCEPTED,
        );
    }
}
