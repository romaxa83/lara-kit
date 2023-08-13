<?php

declare(strict_types=1);

namespace App\GraphQL\Types\Enums\Permissions;

use App\GraphQL\Types\BaseEnumType;
use App\Modules\Admin\Models\Admin;
use Core\Permissions\Permission;
use Core\Services\Permissions\PermissionService;
use Illuminate\Support\Str;

class AdminPermissionEnum extends BaseEnumType
{
    public const NAME = 'AdminPermissionEnum';
    public const DESCRIPTION = 'Список всех возможных разрешений для типа: ' . self::GUARD;
    public const GUARD = Admin::GUARD;

    public function attributes(): array
    {
        return array_merge(
            parent::attributes(),
            [
                'values' => app(PermissionService::class)
                    ->getPermissionsList(static::GUARD)
                    ->sortBy(fn(Permission $p) => $p->getKey())
                    ->mapWithKeys(
                        function (\Core\Permissions\Permission $p) {
                            $key = str_replace(['.', '-'], '_', $p->getKey());

                            return [
                                Str::lower($key) => $p->getKey()
                            ];
                        }
                    )
                    ->toArray()
            ]
        );
    }
}
