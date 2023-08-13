<?php

namespace App\Casts;

use App\ValueObjects\Email;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use InvalidArgumentException;

class EmailCast implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes): Email
    {
        return new Email($attributes['email']);
    }

    public function set($model, string $key, $value, array $attributes): string
    {
        if (is_string($value)) {
            $value = new Email($value);
        }

        if (!$value instanceof Email) {
            throw new InvalidArgumentException(__('exceptions.value_not_email_instance'));
        }

        return (string)$value;
    }
}
