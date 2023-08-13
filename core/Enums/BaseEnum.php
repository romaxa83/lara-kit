<?php

namespace Core\Enums;

use BenSampo\Enum\Enum;
use Illuminate\Validation\Rule;

abstract class BaseEnum extends Enum
{
    public static function listToString(string $delimiter = ', '): string
    {
        return implode($delimiter, self::list());
    }

    public static function list(): array
    {
        return self::getValues();
    }

    public static function ruleIn(): string
    {
        return Rule::in(self::getValues());
    }
}
