<?php

namespace Core\GraphQL\Types;

use GraphQL\Type\Definition\NonNull;
use GraphQL\Type\Definition\NullableType;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;

final class FileType
{
    public const NAME = 'Upload';

    public static function nonNullType(): Type|NonNull
    {
        return Type::nonNull(self::type());
    }

    public static function type(): Type|NullableType
    {
        return GraphQL::type(self::NAME);
    }
}


