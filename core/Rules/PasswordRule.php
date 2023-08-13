<?php

namespace Core\Rules;

use App\Modules\User\Models\User;
use Illuminate\Contracts\Validation\Rule;

class PasswordRule implements Rule
{

    public function passes($attribute, $value): bool
    {
        return preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{' . User::MIN_LENGTH_PASSWORD . ',250}$/', $value);
    }

    public function message(): string
    {
        return __('validation.custom.password.password_rule');
    }
}
