<?php

namespace App\GraphQL\Types\Localization;

use App\GraphQL\Types\BaseType;
use App\GraphQL\Types\NonNullType;
use GraphQL\Type\Definition\Type;

class LanguageType extends BaseType
{
    public const NAME = 'LanguageType';

    public function fields(): array
    {
        return [
            'name' => [
                'type' => NonNullType::string(),
            ],
            'slug' => [
                'type' => NonNullType::string(),
            ],
            'active' => [
                'type' => Type::boolean(),
            ],
            'default' => [
                'type' => Type::boolean(),
            ],
            'sort' => [
                'type' => Type::int(),
            ],
        ];
    }
}
