<?php

declare(strict_types=1);

namespace Core\GraphQL\Helpers;

use Attribute;

#[Attribute]
class GraphQLType extends BaseGraphQLTypeAttributeHelper
{
    public function __construct(
        private mixed $value = null,
        private bool $nullableType = true
    ) {
        parent::__construct($this->value);
    }

    public function isNullable(): bool
    {
        return $this->nullableType;
    }
}
