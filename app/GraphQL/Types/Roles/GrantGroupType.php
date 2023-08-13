<?php

namespace App\GraphQL\Types\Roles;

use App\GraphQL\Types\BaseType;
use App\GraphQL\Types\NonNullType;
use GraphQL\Type\Definition\Type;

class GrantGroupType extends BaseType
{
    public const NAME = 'GrantGroupType';

    public function fields(): array
    {
        return [
            'key' => [
                'type' => Type::id(),
            ],
            'name' => [
                'type' => NonNullType::string(),
            ],
            'position' => [
                'type' => NonNullType::int(),
            ],
            'permissions' => [
                'type' => Type::listOf(GrantType::type()),
            ]
        ];
    }
}
