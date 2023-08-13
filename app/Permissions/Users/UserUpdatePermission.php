<?php

namespace App\Permissions\Users;

use Core\Permissions\BasePermission;

class UserUpdatePermission extends BasePermission
{
    public const KEY = 'user.update';

    public function getName(): string
    {
        return __('permissions.user.grants.update');
    }

    public function getPosition(): int
    {
        return 3;
    }
}
