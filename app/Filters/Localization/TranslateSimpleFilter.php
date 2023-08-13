<?php

namespace App\Filters\Localization;

use EloquentFilter\ModelFilter;
use Illuminate\Support\Str;

class TranslateSimpleFilter extends ModelFilter
{

    public function place(array $place): void
    {
        $this->whereIn('place', $place);
    }

    public function lang(array $lang): void
    {
        $lang = array_map(
            static function ($item) {
                return Str::lower($item);
            },
            $lang
        );

        $this->whereIn('lang', $lang);
    }

}
