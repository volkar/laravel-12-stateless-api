<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Album */
final class AlbumForListResource extends JsonResource
{
    /** @return array<string,mixed> */
    public function toArray(Request $request): array
    {
        // Get cover (first image in album for now)
        $cover = null;
        foreach ($this->atlas as $item) {
            if ('img' === $item['type']) {
                $cover = $item['src'];
                break;
            }
        }
        return [
            'name' => $this->name,
            'cover' => $cover,
            'slug' => $this->slug,
            'date' => new DateResource(
                resource: $this->date_at,
            ),
        ];
    }
}
