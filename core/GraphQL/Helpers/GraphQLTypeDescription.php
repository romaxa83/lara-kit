<?php

declare(strict_types=1);

namespace Core\GraphQL\Helpers;

use Attribute;
use ReflectionClass;

#[Attribute]
class GraphQLTypeDescription extends BaseGraphQLTypeAttributeHelper
{
    public static function readGqlTypeDescription(object $object): ?string
    {
        $reflection = new ReflectionClass($object);
        $reflectionAttributes = $reflection->getAttributes(static::class);

        if ($attrs = array_shift($reflectionAttributes)) {
            return $attrs->newInstance()->getValue();
        }

        return null;
    }
}
