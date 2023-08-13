<?php

namespace App\Permissions\Admins;

use Core\Permissions\BasePermission;

class AdminCreatePermission extends BasePermission
{
    public const KEY = 'admin.create';

    public function getName(): string
    {
        return __('permissions.admin.grants.create');
    }

    public function getPosition(): int
    {
        return 2;
    }
}
