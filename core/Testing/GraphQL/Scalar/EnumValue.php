<?php

namespace Core\Testing\GraphQL\Scalar;

class EnumValue extends Scalar
{
    public function __construct(protected string $value)
    {
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
