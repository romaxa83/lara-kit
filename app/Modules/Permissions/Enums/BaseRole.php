<?php

namespace App\Modules\Permissions\Enums;

use Core\Enums\BaseEnum;

/**
 * @method static static SUPER_ADMIN()
 * @method static static ADMIN()
 * @method static static USER()
 */
class BaseRole extends BaseEnum
{
    public const SUPER_ADMIN = 'super_admin';
    public const ADMIN       = 'admin';
    public const USER        = 'user';

    public function isSuperAdmin(): bool
    {
        return $this->is(self::SUPER_ADMIN());
    }

    public function isAdmin(): bool
    {
        return $this->is(self::ADMIN());
    }

    public function isUser(): bool
    {
        return $this->is(self::USER());
    }

    public static function getGuardByRole(string $role): string
    {
        if($role == self::USER){
            return Guard::USER;
        }

        return Guard::ADMIN;
    }
}


