<?php

namespace App\Permissions\Users;

use Core\Permissions\BasePermission;

class UserCreatePermission extends BasePermission
{
    public const KEY = 'user.create';

    public function getName(): string
    {
        return __('permissions.user.grants.create');
    }

    public function getPosition(): int
    {
        return 2;
    }
}
