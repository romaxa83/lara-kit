<?php

namespace App\Modules\Permissions\Enums;

use Core\Enums\BaseEnum;

/**
 * @method static static ADMIN()
 * @method static static USER()
 */
class Guard extends BaseEnum
{
    public const ADMIN = 'admin_guard';
    public const USER  = 'user_guard';
}
