<?php

declare(strict_types=1);

namespace Core\GraphQL\Helpers;

abstract class BaseGraphQLTypeAttributeHelper
{
    public function __construct(private mixed $value = null)
    {
    }

    public function getValue(): mixed
    {
        return $this->value;
    }
}
