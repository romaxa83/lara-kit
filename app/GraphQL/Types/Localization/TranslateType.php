<?php

namespace App\GraphQL\Types\Localization;

use App\GraphQL\Types\BaseType;
use GraphQL\Type\Definition\Type;
use JetBrains\PhpStorm\ArrayShape;

class TranslateType extends BaseType
{
    public const NAME = 'TranslateType';

    public function fields(): array
    {
        return [
            'place' => [
                'type' => Type::nonNull(Type::string()),
            ],
            'key' => [
                'type' => Type::nonNull(Type::string()),
            ],
            'text' => [
                'type' => Type::string(),
            ],
            'lang' => [
                'type' => Type::nonNull(Type::string()),
            ],
        ];
    }
}
