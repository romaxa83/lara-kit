<?php

namespace App\Permissions\Roles;

use Core\Permissions\BasePermission;

class RoleDeletePermission extends BasePermission
{

    public const KEY = 'role.delete';

    public function getName(): string
    {
        return __('permissions.role.grants.delete');
    }

    public function getPosition(): int
    {
        return 40;
    }
}
