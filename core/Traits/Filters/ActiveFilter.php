<?php

namespace Core\Traits\Filters;

use Illuminate\Database\Eloquent\Builder;

trait ActiveFilter
{
    public function active($value): void
    {
        $this->where('active', $value);
    }
}
