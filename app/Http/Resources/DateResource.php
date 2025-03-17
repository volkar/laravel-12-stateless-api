<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class DateResource extends JsonResource
{
    public function __construct(
        /** @var \Carbon\Carbon */
        public $resource,
    ) {}

    /** @return array<string,mixed> */
    public function toArray(Request $request): array
    {

        // Hardcode locale for now, can be set later based on request's locale from frontend
        $this->resource->locale('ru');

        return [
            'relative' => $this->resource->diffForHumans(),
            'string' => $this->resource->isoFormat('DD/MM/YYYY HH:mm'),
            'date_string' => $this->resource->translatedFormat('d F Y'),
            'timestamp' => $this->resource->timestamp,
        ];
    }
}
