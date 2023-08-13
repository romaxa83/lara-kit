<?php

namespace App\Modules\Localization\Rules;

use Illuminate\Contracts\Validation\Rule;

class ExistsLanguages implements Rule
{

    public function passes($attribute, $value): bool
    {
        return is_support_lang($value);
    }

    public function message(): string
    {
        return __('validation.custom.lang.exist-languages');
    }
}
