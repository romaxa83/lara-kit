<?php


namespace App\GraphQL\Types\Wrappers;


use App\GraphQL\Types\NonNullType;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;

class PaginationMeta
{
    public static function type(string $typeName): Type
    {
        return GraphQL::wrapType(
            $typeName,
            'PaginationMeta',
            PaginationMetaType::class
        );
    }

    public static function nonNullType(string $typeName): NonNullType|Type
    {
        return Type::nonNull(static::type($typeName));
    }
}
