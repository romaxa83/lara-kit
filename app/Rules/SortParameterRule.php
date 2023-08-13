<?php

namespace App\Rules;

use App\Enums\OrderDirectionEnum;
use Illuminate\Contracts\Validation\Rule;

class SortParameterRule implements Rule
{
    private string $message;

    public function __construct(private array $allowedFields = [])
    {
    }

    public function passes($attribute, $value): bool
    {
        [$field, $direction] = explode('-', $value);

        if (!in_array($field, $this->allowedFields, true)) {
            $this->message = (string)__('validation.sorting.incorrect-field');

            return false;
        }

        if (!in_array($direction, OrderDirectionEnum::asArray(), true)) {
            $this->message = (string)__('validation.sorting.incorrect-direction');

            return false;
        }

        return true;
    }

    public function message(): string
    {
        return $this->message ?? __('validation.sorting.incorrect-parameter');
    }
}
