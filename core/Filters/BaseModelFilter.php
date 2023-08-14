<?php

namespace Core\Filters;

use Core\Traits\Filters\IdFilterTrait;
use Core\Traits\Filters\SortFilterTrait;
use EloquentFilter\ModelFilter;

abstract class BaseModelFilter extends ModelFilter
{
    use SortFilterTrait;
    use IdFilterTrait;
}
