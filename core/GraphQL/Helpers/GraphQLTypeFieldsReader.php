<?php

declare(strict_types=1);

namespace Core\GraphQL\Helpers;

use App\GraphQL\Types\NonNullType;
use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\NullableType;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Contracts\TypeConvertible;
use Rebing\GraphQL\Support\Facades\GraphQL;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionUnionType;

class GraphQLTypeFieldsReader
{
    public static function readGqlTypeFields(object $object): array
    {
        $fields = [];

        $reflection = new ReflectionClass($object);

        foreach ($reflection->getProperties() as $property) {
            $type = static::guessTypeByReflectionProperty($property);

            if (is_null($type)) {
                continue;
            }

            $fields[$property->getName()] = [
                'type' => $type,
            ];
        }

        return $fields;
    }

    protected static function guessTypeByReflectionProperty(ReflectionProperty $property): NullableType|Type|null
    {
        return static::guessGqlTypeByAnnotation($property->getAttributes())
            ?: static::guessGqlTypeByReflectionTypes($property->getType());
    }

    /**
     * @param ReflectionAttribute[] $attributes
     * @return Type|NullableType|null
     */
    protected static function guessGqlTypeByAnnotation(array $attributes): Type|NullableType|null
    {
        foreach ($attributes as $attribute) {
            $attributeClass = $attribute->getName();
            if (is_a($attributeClass, GraphQLType::class, true)) {
                [$typeClass, $nullable] = $attribute->getArguments();
                if (is_a($typeClass, Type::class, true)) {
                    return static::extractSimpleType($nullable, new $typeClass());
                }

                if (is_a($typeClass, TypeConvertible::class, true)) {
                    return static::extractConvertibleType($nullable, $typeClass);
                }
            }

            if (is_a($attributeClass, GraphQLListType::class, true)) {
                [$nullableList, $typeClass, $nullable] = $attribute->getArguments();

                if (is_a($typeClass, Type::class, true)) {
                    $type = static::extractSimpleType($nullable, new $typeClass());

                    return static::extractListType($nullableList, $type);
                }

                if (is_a($typeClass, TypeConvertible::class, true)) {
                    $type = static::extractConvertibleType($nullable, $typeClass);

                    return static::extractListType($nullableList, $type);
                }
            }
        }

        return null;
    }

    protected static function extractSimpleType(bool $nullable, Type|NullableType $type): Type
    {
        return GraphQL::type($nullable ? $type->name : Type::nonNull($type)->toString());
    }

    protected static function extractConvertibleType(
        bool $nullable,
        string|\Core\GraphQL\Types\GqlType $typeClass
    ): Type|NullableType {
        return $nullable ? $typeClass::type() : $typeClass::nonNullType();
    }

    protected static function extractListType(bool $nullableList, Type $type): ListOfType|Type
    {
        return $nullableList ? Type::listOf($type) : NonNullType::listOf($type);
    }

    protected static function guessGqlTypeByReflectionTypes(
        ReflectionNamedType|ReflectionUnionType|null $type
    ): Type|NullableType|null {
        if (is_null($type)) {
            return null;
        }

        if ($type instanceof ReflectionNamedType) {
            $nullable = $type->allowsNull();

            if ($convertible = static::guessConvertibleType([$type])) {
                return static::extractConvertibleType($nullable, $convertible);
            }

            return static::guessGqlTypeByStringType($type->getName(), $nullable);
        }

        if ($type instanceof ReflectionUnionType) {
            $types = $type->getTypes();
            $nullable = static::isNullable($types);

            if ($convertible = static::guessConvertibleType($types)) {
                return static::extractConvertibleType($nullable, $convertible);
            }

            if ($typeClass = static::guessTypeByPhpTypes($types)) {
                return static::extractSimpleType($nullable, new $typeClass());
            }

            return static::guessGqlTypeByStringType(
                static::getFirstSimpleType($types),
                $nullable
            );
        }

        return Type::string();
    }

    protected static function guessConvertibleType(array $types): null|string|TypeConvertible
    {
        foreach ($types as $type) {
            $class = $type->getName();

            if (is_a($class, TypeConvertible::class, true)) {
                return $class;
            }
        }

        return null;
    }

    protected static function guessGqlTypeByStringType(string $typeName, bool $nullable): Type
    {
        /** @var Type|NonNullType $typeClass */
        $typeClass = $nullable
            ? Type::class
            : NonNullType::class;

        return match ($typeName) {
            'bool' => $typeClass::boolean(),
            'float' => $typeClass::float(),
            'int' => $typeClass::int(),
            default => $typeClass::string(),
        };
    }

    /**
     * @param ReflectionNamedType[] $types
     * @return bool
     */
    protected static function isNullable(array $types): bool
    {
        foreach ($types as $type) {
            if ($type->allowsNull()) {
                return true;
            }
        }

        return false;
    }

    protected static function guessTypeByPhpTypes(array $types): ?string
    {
        foreach ($types as $type) {
            $class = $type->getName();

            if (is_a($class, Type::class, true)) {
                return $class;
            }
        }
        return null;
    }

    /**
     * @param ReflectionNamedType[] $types
     * @return Type|null
     */
    protected static function getFirstSimpleType(array $types): ?string
    {
        foreach ($types as $type) {
            if ($type->isBuiltin()) {
                return $type->getName();
            }
        }

        return null;
    }
}
