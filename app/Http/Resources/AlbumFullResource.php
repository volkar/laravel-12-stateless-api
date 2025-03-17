<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Album */
final class AlbumFullResource extends JsonResource
{
    /** @return array<string,mixed> */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'theme' => $this->theme,
            'atlas' => $this->atlas,
            'date' => new DateResource(
                resource: $this->date_at,
            ),
        ];
    }
}
