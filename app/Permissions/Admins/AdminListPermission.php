<?php

namespace App\Permissions\Admins;

use Core\Permissions\BasePermission;

class AdminListPermission extends BasePermission
{
    public const KEY = 'admin.list';

    public function getName(): string
    {
        return __('permissions.admin.grants.list');
    }

    public function getPosition(): int
    {
        return 1;
    }
}
