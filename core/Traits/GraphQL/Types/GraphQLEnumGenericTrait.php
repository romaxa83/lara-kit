<?php

declare(strict_types=1);

namespace Core\Traits\GraphQL\Types;

use Core\GraphQL\Helpers\GraphQLTypeDescription;
use Core\GraphQL\Helpers\GraphQLTypeName;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\NonNull;
use GraphQL\Type\Definition\NullableType;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use ReflectionClass;

trait GraphQLEnumGenericTrait
{
    public function __construct($enumValue = null)
    {
        if (is_null($enumValue)) {
            $enumValue = static::getValues()[0];
        }

        parent::__construct($enumValue);
    }

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

    public function toType(): Type
    {
        return new EnumType(
            [
                'name' => $this->getGraphQLTypeName(),
                'description' => $this->getGraphQlTypeDescription(),
                'values' => $this->getGraphQLTypeEnumValues(),
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

    protected function getGraphQLTypeEnumValues(): array
    {
        return collect(self::getValues())
            ->mapWithKeys(static fn(string $type) => [$type => $type])
            ->toArray();
    }

    public function __get(string $name)
    {
        if ($name === 'name') {
            return $this->getGraphQLTypeName();
        }

        if ($name === 'description') {
            return $this->getGraphQlTypeDescription();
        }

        if ($name === 'values') {
            return $this->getGraphQLTypeEnumValues();
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
