<?php

declare(strict_types=1);

namespace Core\GraphQL\Types;

use GraphQL\Type\Definition\NonNull;
use GraphQL\Type\Definition\NullableType;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Contracts\TypeConvertible;

interface GraphQLType extends TypeConvertible, NullableType
{
    public static function type(): Type|NullableType;

    public static function nonNullType(): Type|NonNull;
}
