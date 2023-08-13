<?php

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\Type;

abstract class BaseMultilangType extends BaseType
{
    public function fields(): array
    {
        return array_merge(
            parent::fields(),
            [
                'translate' => [
                    'type' => Type::nonNull($this->getTranslationType()),
                    'is_relation' => true,
                ],
                'translates' => [
                    'type' => Type::nonNull(Type::listOf($this->getTranslationType())),
                    'is_relation' => true,
                ]
            ]
        );
    }

    abstract protected function getTranslationType(): Type;
}
