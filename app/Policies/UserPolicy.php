<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\User;

final class UserPolicy
{
    public function update(User $user, User $model): bool
    {
        // Self-update or admin
        return $user->id === $model->id || $user->role === RoleEnum::ADMIN->value;
    }

    public function delete(User $user, User $model): bool
    {
        // Self-delete or admin
        return $user->id === $model->id || $user->role === RoleEnum::ADMIN->value;
    }

    public function restore(User $user, User $model): bool
    {
        // Only admin can restore user
        return $user->role === RoleEnum::ADMIN->value;
    }

    public function forceDelete(User $user, User $model): bool
    {
        // Only admin can force delete user
        return $user->role === RoleEnum::ADMIN->value;
    }
}
