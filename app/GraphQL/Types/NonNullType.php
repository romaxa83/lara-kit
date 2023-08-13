<?php

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\Type;

class NonNullType
{
    public static function int(): Type
    {
        return Type::nonNull(Type::int());
    }

    public static function float(): Type
    {
        return Type::nonNull(Type::float());
    }

    public static function id(): Type
    {
        return Type::nonNull(Type::id());
    }

    public static function boolean(): Type
    {
        return Type::nonNull(Type::boolean());
    }

    public static function string(): Type
    {
        return Type::nonNull(Type::string());
    }

    public static function listOfString(): Type
    {
        return self::listOf(
            Type::string()
        );
    }

    public static function listOf(Type $type): Type
    {
        return Type::nonNull(Type::listOf($type));
    }
}
