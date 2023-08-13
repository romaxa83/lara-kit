<?php

namespace App\Permissions\Admins;

use Core\Permissions\BasePermissionGroup;

class AdminPermissionsGroup extends BasePermissionGroup
{
    public const KEY = 'admin';

    public function getName(): string
    {
        return __('permissions.admin.group');
    }

    public function getPosition(): int
    {
        return 0;
    }
}
