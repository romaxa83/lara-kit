<?php

namespace App\GraphQL\Types\Roles;

use App\GraphQL\Types\BaseType;
use App\GraphQL\Types\Enums\Permissions\GuardEnum;
use App\GraphQL\Types\NonNullType;
use App\Modules\Permissions\Models\Role;

class RoleType extends BaseType
{
    public const NAME = 'RoleType';
    public const MODEL = Role::class;

    public function fields(): array
    {
        return array_merge(
            parent::fields(),
            [
                'name' => [
                    'type' => NonNullType::string(),
                ],
                'guard' => [
                    'alias' => 'guard_name',
                    'type' => GuardEnum::Type(),
                ],
                'translation' => [
                    'type' => RoleTranslationType::type(),
                    'is_relation' => true,
                ],
                'translations' => [
                    'type' => RoleTranslationType::nonNullList(),
                    'is_relation' => true,
                ],
                'permissions' => [
                    'type' => PermissionType::nonNullList(),
                    'is_relation' => true,
                ]
            ]
        );
    }
}
