<?php

namespace App\Filters\Localization;

use App\Traits\Filter\SortFilterTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class TranslateAdminFilter extends TranslateSimpleFilter
{
    use SortFilterTrait;

    public function key(string $key): void
    {
        $key = Str::lower($key);

        $this->where(
            function (Builder $builder) use ($key) {
                $builder->orWhereRaw('LOWER(key) LIKE ?', ["%$key%"]);
            }
        );
    }

    public function text(string $text): void
    {
        $text = Str::lower($text);

        $this->where(
            function (Builder $builder) use ($text) {
                $builder->orWhereRaw('LOWER(text) LIKE ?', ["%$text%"]);
            }
        );
    }

    protected function allowedOrders(): array
    {
        return ['key', 'text', 'lang'];
    }
}
