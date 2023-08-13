<?php

namespace App\GraphQL\InputTypes\Permissions;

use App\GraphQL\Types\BaseInputType;
use App\GraphQL\Types\NonNullType;
use GraphQL\Type\Definition\Type;

class RoleCreateInput extends BaseInputType
{
    public const NAME = 'RoleCreateInputType';

    public function fields(): array
    {
        return [
            'name' => [
                'type' => NonNullType::string(),
            ],
            'guard' => [
                'type' => NonNullType::string(),
            ],
            'permissions' => [
                'type' => Type::listOf(Type::string())
            ],
            'translations' => [
                'type' => RoleTranslationInput::nonNullList(),
            ],
        ];
    }
}


