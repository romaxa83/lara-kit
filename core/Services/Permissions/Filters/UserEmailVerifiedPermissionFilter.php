<?php

namespace Core\Services\Permissions\Filters;

use App\Modules\User\Models\User;
use Illuminate\Support\Collection;

class UserEmailVerifiedPermissionFilter extends BasePermissionFilter
{
    public function filter(User $user, Collection $permissions): Collection
    {
        if (!$user->isOwner()) {
            return $permissions;
        }

        return $user->isEmailVerified()
            ? $permissions
            : $this->filterPermissions($permissions);
    }
}
