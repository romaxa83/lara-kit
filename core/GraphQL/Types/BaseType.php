<?php

namespace Core\GraphQL\Types;

use Core\Traits\GraphQL\Types\BaseTypeTrait;
use GraphQL\Type\Definition\NullableType;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

abstract class BaseType extends GraphQLType implements NullableType
{
    use BaseTypeTrait;

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::id()),
            ],
            'created_at' => [
                'type' => Type::nonNull(Type::string()),
            ],
            'updated_at' => [
                'type' => Type::nonNull(Type::string()),
            ],
        ];
    }
}

