<?php

namespace App\GraphQL\Types\Roles;

use App\GraphQL\Types\BaseType;
use App\GraphQL\Types\NonNullType;

class GrantType extends BaseType
{
    public const NAME = 'GrantType';

    public function fields(): array
    {
        return [
            'key' => [
                'type' => NonNullType::string(),
            ],
            'name' => [
                'type' => NonNullType::string(),
            ],
            'position' => [
                'type' => NonNullType::int(),
            ]
        ];
    }
}
