<?php

namespace App\GraphQL\InputTypes\Permissions;

use App\GraphQL\Types\BaseInputType;
use App\GraphQL\Types\NonNullType;
use GraphQL\Type\Definition\Type;

class RoleUpdateInput extends BaseInputType
{
    public const NAME = 'RoleUpdateInputType';

    public function fields(): array
    {
        return [
            'name' => [
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
