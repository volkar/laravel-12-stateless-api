<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\Album;
use App\Models\User;

final class AlbumPolicy
{
    public function create(User $user): bool
    {
        // Only verified users can create albums
        return $user->hasVerifiedEmail();
    }

    public function update(User $user, Album $album): bool
    {
        // Only owner can update album
        return $user->id === $album->user_id;
    }

    public function delete(User $user, Album $album): bool
    {
        // Only owner can delete album
        return $user->id === $album->user_id;
    }

    public function restore(User $user, Album $album): bool
    {
        // Only owner can restore album
        return $user->id === $album->user_id;
    }

    public function forceDelete(User $user, Album $album): bool
    {
        // Only admin can force delete album
        return $user->role === RoleEnum::ADMIN->value;
    }
}
