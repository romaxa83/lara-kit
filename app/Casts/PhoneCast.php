<?php

namespace App\Casts;

use App\ValueObjects\Phone;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use InvalidArgumentException;

class PhoneCast implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes): ?Phone
    {
        return is_null($value) ? null : new Phone($value);
    }

    public function set($model, string $key, $value, array $attributes): string
    {
        if (!$value instanceof Phone && !is_null($value)) {
            throw new InvalidArgumentException(__('exceptions.value_not_phone_instance'));
        }

        return (string)$value;
    }
}

