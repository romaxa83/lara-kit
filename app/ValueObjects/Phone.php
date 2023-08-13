<?php

namespace App\ValueObjects;

use Core\ValueObjects\AbstractValueObject;
use InvalidArgumentException;

class Phone extends AbstractValueObject
{
    protected function filter(string $value): string
    {
        $value = phone_clear($value);

        return parent::filter($value);
    }

    protected function validate(string $value): void
    {
        if (!preg_match('/^\d{9,20}$/', $value)) {
            throw new InvalidArgumentException(__('exceptions.value_must_be_phone') . ' ' . $value);
        }
    }
}
