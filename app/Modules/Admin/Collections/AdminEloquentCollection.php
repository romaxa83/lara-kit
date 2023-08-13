<?php

namespace App\Modules\Admin\Collections;

use App\Modules\Admin\Models\Admin;
use ArrayIterator;
use Illuminate\Database\Eloquent\Collection;

/**
 * @method Admin|null first(callable $callback = null, $default = null)
 * @method Admin|null last(callable $callback = null, $default = null)
 * @method Admin|null pop()
 * @method Admin|null shift()
 * @method ArrayIterator|Admin[] getIterator()
 */
class AdminEloquentCollection extends Collection
{}
