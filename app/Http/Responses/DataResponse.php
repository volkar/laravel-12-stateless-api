<?php

declare(strict_types=1);

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final readonly class DataResponse implements Responsable
{
    public function __construct(
        private mixed $data,
        private int $status = Response::HTTP_OK,
    ) {}

    public function toResponse($request)
    {
        return new JsonResponse(
            data: $this->data,
            status: $this->status,
        );
    }
}
