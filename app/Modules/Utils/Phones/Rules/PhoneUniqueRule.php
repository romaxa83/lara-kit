<?php

namespace App\Modules\Utils\Phones\Rules;

use App\Modules\Utils\Phones\Models\Phone;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Eloquent\Builder;

class PhoneUniqueRule implements Rule
{
    public function __construct(
        protected string $modelClass,
        protected string $ignoreField = 'model_id',
        protected null|string|int $ignoreValue = null
    )
    {}

    protected  $value;

    public function passes($attribute, $value): bool
    {

        return !Phone::query()
            ->where('model_type', $this->modelClass::MORPH_NAME)
            ->when($this->ignoreValue,
                fn(Builder $b) => $b->whereNot($this->ignoreField, $this->ignoreValue)
            )
            ->exists();
    }

    public function message(): string
    {
        return __('validation.unique_phone');
    }
}
