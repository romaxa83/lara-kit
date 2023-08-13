<?php

namespace Core\Filters;

use App\Traits\Filter\IdFilterTrait;
use App\Traits\Filter\SortFilterTrait;
use EloquentFilter\ModelFilter;

abstract class BaseModelFilter extends ModelFilter
{
    use SortFilterTrait;
    use IdFilterTrait;
}
