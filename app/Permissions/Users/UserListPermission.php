<?php

namespace App\Permissions\Users;

use Core\Permissions\BasePermission;

class UserListPermission extends BasePermission
{
    public const KEY = 'user.list';

    public function getName(): string
    {
        return __('permissions.user.grants.list');
    }

    public function getPosition(): int
    {
        return 1;
    }
}
