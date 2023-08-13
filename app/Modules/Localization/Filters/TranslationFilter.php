<?php

namespace App\Modules\Localization\Filters;

use App\Modules\Localization\Models\Translation;
use Core\Filters\BaseModelFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class TranslationFilter extends BaseModelFilter
{
    public function place(array $values): void
    {
        $this->whereIn('place', $values);
    }

    public function lang(array $values): void
    {
        $values = array_map(
            static function ($item) {
                return Str::lower($item);
            },
            $values
        );

        $this->whereIn('lang', $values);
    }

    public function key(string $value): void
    {
        $value = Str::lower($value);

        $this->where(
            fn(Builder $builder) => $builder->orWhereRaw('LOWER(`key`) LIKE ?', ["%$value%"])
        );
    }

    public function text(string $value): void
    {
        $value = Str::lower($value);

        $this->where(
            fn(Builder $builder) => $builder->orWhereRaw('LOWER(`text`) LIKE ?', ["%$value%"])
        );
    }

    protected function allowedOrders(): array
    {
        return Translation::ALLOWED_SORTING_FIELDS;
    }
}
