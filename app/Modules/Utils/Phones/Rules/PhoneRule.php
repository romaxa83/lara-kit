<?php

namespace App\Modules\Utils\Phones\Rules;


use App\Modules\Utils\Phones\ValueObject\Phone;
use Illuminate\Contracts\Validation\Rule;
use Throwable;

class PhoneRule implements Rule
{
    protected  $value;

    public function passes($attribute, $value): bool
    {
        $this->value = $value;
        try {

            new Phone($value);

            return true;
        } catch (Throwable) {
            return false;
        }
    }

    public function message(): string
    {
        return __('validation.custom.phone.not_valid', ['value' => $this->value]);
    }
}

