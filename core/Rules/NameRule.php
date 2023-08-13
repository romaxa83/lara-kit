<?php

namespace Core\Rules;

use Illuminate\Contracts\Validation\Rule;

class NameRule implements Rule
{
    public function __construct(private string $attribute = 'name')
    {
    }

    public function passes($attribute, $value): bool
    {
        return preg_match('/^[a-zA-Z\x{0400}-\x{04FF} \-\']{2,250}$/u', $value);
    }

    public function message(): string
    {
        return __('validation.custom.name.name_rule', ['attribute' => __('validation.attributes.' . $this->attribute)]);
    }
}
