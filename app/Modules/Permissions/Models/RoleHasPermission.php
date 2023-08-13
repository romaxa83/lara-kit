<?php

declare(strict_types=1);

namespace App\Modules\Permissions\Models;

use Core\Models\BasePivot;

/**
 * @property int role_id
 * @property int permission_id
 */
class RoleHasPermission extends BasePivot
{
    public const TABLE = 'role_has_permissions';

    public $timestamps = false;

    protected $table = self::TABLE;
}
