<?php

namespace Core\Rules;

use Illuminate\Contracts\Validation\Rule;

class TranslatesArrayValidator implements Rule
{
    public function passes($attribute, $value): bool
    {
        $givenLang = array_column($value,'lang');

        return array_diff(app_languages(value: 'slug'), $givenLang) === array_diff($givenLang, app_languages(value: 'slug'));
    }

    public function message(): string
    {
        return __('validation.custom.translations.not_contain_supporting_lang');
    }
}
