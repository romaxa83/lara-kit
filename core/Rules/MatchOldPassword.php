<?php

namespace Core\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class MatchOldPassword implements Rule
{

    public function __construct(private string $guard)
    {
    }

    public function passes($attribute, $value): bool
    {
        return Hash::check(
            $value,
            auth()->guard($this->guard)
                ->user()
                ?->getAuthPassword()
        );
    }

    public function message(): string
    {
        return __('auth.password');
    }
}
