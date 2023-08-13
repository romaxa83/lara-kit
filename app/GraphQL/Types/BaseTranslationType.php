<?php

namespace App\GraphQL\Types;

abstract class BaseTranslationType extends BaseType
{
    public function fields(): array
    {
        return [
            'id' => [
                'type' => NonNullType::id(),
            ],
            'language' => [
                'type' => NonNullType::string(),
            ]
        ];
    }
}
