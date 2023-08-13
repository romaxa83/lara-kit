<?php

namespace App\Modules\Localization\Filters;

use App\Modules\Localization\Models\Language;
use Core\Filters\BaseModelFilter;
use Core\Traits\Filters\ActiveFilter;

class LanguageFilter extends BaseModelFilter
{
    use ActiveFilter;

    protected function allowedOrders(): array
    {
        return Language::ALLOWED_SORTING_FIELDS;
    }
}
