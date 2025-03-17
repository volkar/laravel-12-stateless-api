<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\AccessEnum;
use App\Models\User;

final class AccessService
{
    /** @param string $access
     * @param array<string> $sharedFor
     * @param User $authorUser
     * @param User|null $viewerUser
     * @return bool */
    public static function checkAccess(string $access, array $sharedFor, User $authorUser, User|null $viewerUser = null): bool
    {
        if ($viewerUser && $authorUser->id === $viewerUser->id) {
            // Owner, allow access
            return true;
        }

        if ($access === AccessEnum::PUBLIC->value) {
            // Public, allow access for all
            return true;
        }

        if (null !== $viewerUser) {
            if ($access === AccessEnum::PRIVATE->value) {
                // Private, no access
                return false;
            }

            if ($access === AccessEnum::SHARED->value && count($sharedFor) > 0) {
                // Shared for list of emails or groups

                if ( ! $viewerUser->email_verified_at) {
                    // Email not verified, no access to shared resource
                    return false;
                }

                $sharedForEmails = [];

                foreach ($sharedFor as $sharedItem) {
                    if (str_contains($sharedItem, '@')) {
                        // Regular email, add to list
                        $sharedForEmails[] = $sharedItem;
                    } else {
                        // User group, get emails from owner's groups
                        foreach ($authorUser->user_groups as $group) {
                            if ($group['slug'] === $sharedItem && is_array($group['list'])) {
                                $sharedForEmails = array_merge($sharedForEmails, $group['list']);
                            }
                        }
                    }
                }

                // If viewer's email is in shared for list, allow access
                return in_array($viewerUser->email, $sharedForEmails);
            }
        }

        return false;
    }
}
