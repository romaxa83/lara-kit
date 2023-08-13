<?php

namespace Core\Services\Permissions;

use App\Exceptions\Permissions\RoleForOwnerException;
use App\Modules\Permissions\Models\Role;
use App\Modules\User\Models\User;
use Throwable;

class RoleService
{
    /**
     * @throws Throwable
     */
    public function setAsDefaultForOwner(Role $role): void
    {
        if ($role->isForOwner()) {
            throw new RoleForOwnerException();
        }

        $role->setAsDefaultForOwner();
    }

    public function assignDefaultRole(User $user): void
    {
        $user->assignRole(
            Role::query()
                ->forUsers()
                ->defaultForOwner()
                ->firstOrFail()
        );
    }
}
