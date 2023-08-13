<?php

namespace App\Filters\Localization;

use App\Models\Localization\Locale;
use App\Traits\Filter\IdFilterTrait;
use App\Traits\Filter\SortFilterTrait;
use EloquentFilter\ModelFilter;

class LocaleFilter extends ModelFilter
{
    use SortFilterTrait;
    use IdFilterTrait;

    public function slug(string $slug): void
    {
        $this->where('slug', $slug);
    }

    protected function allowedOrders(): array
    {
        return Locale::ALLOWED_SORTING_FIELDS;
    }
}
