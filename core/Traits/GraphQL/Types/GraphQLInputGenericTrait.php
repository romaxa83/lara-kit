<?php

declare(strict_types=1);

namespace Core\Traits\GraphQL\Types;

use App\GraphQL\Types\NonNullType;
use Core\GraphQL\Helpers\GraphQLTypeDescription;
use Core\GraphQL\Helpers\GraphQLTypeName;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\NonNull;
use GraphQL\Type\Definition\NullableType;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Contracts\TypeConvertible;
use Rebing\GraphQL\Support\Facades\GraphQL;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionUnionType;

trait GraphQLInputGenericTrait
{
    public static function nonNullType(): NonNull|Type
    {
        return Type::nonNull(static::type());
    }

    public static function type(): Type|NullableType
    {
        return GraphQL::type(
            self::getGraphQLTypeInstance()->getGraphQLTypeName()
        );
    }

    protected function getGraphQLTypeName(): string
    {
        $reflection = new ReflectionClass($this);
        $reflectionAttributes = $reflection->getAttributes(GraphQLTypeName::class);

        if ($attrs = array_shift($reflectionAttributes)) {
            return $attrs->newInstance()->getValue();
        }

        return $reflection->getShortName();
    }

    protected static function getGraphQLTypeInstance(): static
    {
        return new static();
    }

    public function toType(): Type|InputObjectType
    {
        return new InputObjectType(
            [
                'name' => $this->getGraphQLTypeName(),
                'description' => $this->getGraphQlTypeDescription(),
                'fields' => $this->getGraphQLTypeFields()
            ]
        );
    }

    protected function getGraphQlTypeDescription(): ?string
    {
        $reflection = new ReflectionClass($this);
        $reflectionAttributes = $reflection->getAttributes(GraphQLTypeDescription::class);

        if ($attrs = array_shift($reflectionAttributes)) {
            return $attrs->newInstance()->getValue();
        }

        return null;
    }

    protected function getGraphQLTypeFields(): array
    {
        $fields = [];

        $reflection = new ReflectionClass($this);

        foreach ($reflection->getProperties() as $property) {
            $type = $this->defineGraphQLTypeByReflectionTypes($property->getType());
            if (is_null($type)) {
                continue;
            }

            $fields[$property->getName()] = [
                'type' => $type,
            ];
        }

        return $fields;
    }

    protected function defineGraphQLTypeByReflectionTypes(
        ReflectionNamedType|ReflectionUnionType|null $type
    ): Type|NullableType|null {
        if (is_null($type)) {
            return null;
        }

        if ($type instanceof ReflectionNamedType) {
            $typeName = $type->getName();
            $nullable = $type->allowsNull();

            return $this->defineGraphQLTypeByStringType($typeName, $nullable);
        }

        if ($type instanceof ReflectionUnionType) {
            $types = $type->getTypes();
            $nullable = $this->isNullable($types);

            if ($convertible = $this->getConvertibleType($types)) {
                return $nullable
                    ? $convertible::type()
                    : Type::nonNull($convertible::type());
            }

            return $this->defineGraphQLTypeByStringType(
                $this->getFirstSimpleType($types),
                $nullable
            );
        }

        return Type::string();
    }

    protected function defineGraphQLTypeByStringType(string $typeName, bool $nullable): Type
    {
        /** @var Type|NonNullType $typeClass */
        $typeClass = $nullable
            ? Type::class
            : NonNullType::class;

        if ($typeName === 'string') {
            return $typeClass::string();
        }

        if ($typeName === 'int') {
            return $typeClass::int();
        }

        if ($typeName === 'float') {
            return $typeClass::float();
        }

        return Type::string();
    }

    /**
     * @param ReflectionNamedType[] $types
     * @return bool
     */
    public function isNullable(array $types): bool
    {
        foreach ($types as $type) {
            if ($type->allowsNull()) {
                return true;
            }
        }

        return false;
    }

    protected function getConvertibleType(array $types): null|string|TypeConvertible
    {
        foreach ($types as $type) {
            $class = $type->getName();

            if (is_a($class, TypeConvertible::class, true)) {
                return $class;
            }
        }

        return null;
    }

    /**
     * @param ReflectionNamedType[] $types
     * @return Type|null
     */
    protected function getFirstSimpleType(array $types): ?string
    {
        foreach ($types as $type) {
            if ($type->isBuiltin()) {
                return $type->getName();
            }
        }

        return null;
    }

    public function __get(string $name)
    {
        if ($name === 'name') {
            return $this->getGraphQLTypeName();
        }

        if ($name === 'description') {
            return $this->getGraphQlTypeDescription();
        }

        return null;
    }

    public function __set(string $name, $value): void
    {
    }

    public function __isset(string $name): bool
    {
        return false;
    }
}
