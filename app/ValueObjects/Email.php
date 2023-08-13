<?php

namespace App\ValueObjects;

use Core\ValueObjects\AbstractValueObject;
use InvalidArgumentException;

class Email extends AbstractValueObject
{
    protected function validate(string $value): void
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException(__('exceptions.value_must_be_email'));
        }
    }
}
