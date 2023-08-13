<?php

declare(strict_types=1);

namespace Core\GraphQL\Types\Enums;

use GraphQL\Type\Definition\EnumType;

class GraphQLTypeCastedEnum extends EnumType
{
    public function serialize($value)
    {
        return !is_string($value) ? parent::serialize($value->value) : parent::serialize($value);
    }
}
