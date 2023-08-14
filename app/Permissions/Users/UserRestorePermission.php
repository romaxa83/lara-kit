<?php

namespace App\Permissions\Users;

use Core\Permissions\BasePermission;

class UserRestorePermission extends BasePermission
{
    public const KEY = 'user.restore';

    public function getName(): string
    {
        return __('permissions.user.grants.restore');
    }

    public function getPosition(): int
    {
        return 3;
    }
}

