<?php

declare(strict_types=1);

namespace Core\GraphQL\Helpers;

use Attribute;
use ReflectionClass;

#[Attribute]
class GraphQLTypeName extends BaseGraphQLTypeAttributeHelper
{
    public static function readGqlTypeName(object $object): string
    {
        $reflection = new ReflectionClass($object);
        $reflectionAttributes = $reflection->getAttributes(static::class);

        if ($attrs = array_shift($reflectionAttributes)) {
            return $attrs->newInstance()->getValue();
        }

        return $reflection->getShortName();
    }
}
