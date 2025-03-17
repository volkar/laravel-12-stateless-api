<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\User */
final class UserSelfResource extends JsonResource
{
    public static $wrap = null;

    /** @return array<string,mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'theme' => $this->theme,
            'user_groups' => $this->user_groups,
            'email' => $this->email,
            'verified' => null !== $this->email_verified_at,
        ];
    }
}
