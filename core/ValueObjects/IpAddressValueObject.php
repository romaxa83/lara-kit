<?php

namespace Core\ValueObjects;

use InvalidArgumentException;

class IpAddressValueObject extends AbstractValueObject
{
    protected function validate(string $value): void
    {
        if (!filter_var($value, FILTER_VALIDATE_IP)) {
            throw new InvalidArgumentException(__('validation.ip', ['attribute' => 'ip']));
        }
    }
}
