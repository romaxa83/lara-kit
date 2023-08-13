<?php

namespace App\Permissions\Users;

use Core\Permissions\BasePermission;

class UserDeletePermission extends BasePermission
{
    public const KEY = 'user.delete';

    public function getName(): string
    {
        return __('permissions.user.grants.delete');
    }

    public function getPosition(): int
    {
        return 4;
    }
}
