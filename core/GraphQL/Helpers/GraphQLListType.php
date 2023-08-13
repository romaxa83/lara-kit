<?php

declare(strict_types=1);

namespace Core\GraphQL\Helpers;

use Attribute;

#[Attribute]
class GraphQLListType extends BaseGraphQLTypeAttributeHelper
{
    public function __construct(
        private bool $nullableList,
        private mixed $value,
        private bool $nullableType
    ) {
        parent::__construct($this->value);
    }

    public function isNullableType(): bool
    {
        return $this->nullableType;
    }

    public function isNullableList(): bool
    {
        return $this->nullableList;
    }
}
