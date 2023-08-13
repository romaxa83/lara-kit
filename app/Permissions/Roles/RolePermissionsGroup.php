<?php

namespace App\Permissions\Roles;

use Core\Permissions\BasePermissionGroup;

class RolePermissionsGroup extends BasePermissionGroup
{
    public const KEY = 'role';

    public function getName(): string
    {
        return __('permissions.role.group');
    }

}
