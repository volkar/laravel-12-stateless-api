<?php

declare(strict_types=1);

namespace App\Http\Controllers\Albums;

use App\Http\Requests\Albums\CreateAlbumRequest;
use App\Http\Responses\ErrorResponse;
use App\Http\Responses\MessageResponse;
use App\Jobs\Albums\CreateAlbumJob;
use App\Models\Album;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

final class CreateAlbumController
{
    public function __construct(
        private readonly Dispatcher $dispatcher,
    ) {}

    public function __invoke(CreateAlbumRequest $request): ErrorResponse|MessageResponse
    {
        // Not allowed by AlbumPolicy, return error response
        if ( ! Gate::allows('create', Album::class)) {
            return new ErrorResponse(
                message: __('albums.create.failure'),
                status: Response::HTTP_FORBIDDEN,
            );
        }

        // Create album by dispatching a job
        $this->dispatcher->dispatch(
            command: new CreateAlbumJob(
                payload: $request->payload(),
            ),
        );

        // Return success response
        return new MessageResponse(
            message: __('albums.create.success'),
            status: Response::HTTP_ACCEPTED,
        );
    }
}
