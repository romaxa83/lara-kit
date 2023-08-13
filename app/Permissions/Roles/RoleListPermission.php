<?php

namespace App\Permissions\Roles;

use Core\Permissions\BasePermission;

class RoleListPermission extends BasePermission
{
    public const KEY = 'role.list';

    public function getName(): string
    {
        return __('permissions.role.grants.list');
    }

    public function getPosition(): int
    {
        return 10;
    }
}
