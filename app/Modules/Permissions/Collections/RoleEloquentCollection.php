<?php

namespace App\Modules\Permissions\Collections;

use App\Modules\Permissions\Models\Role;
use ArrayIterator;
use Illuminate\Database\Eloquent\Collection;

/**
 * @method Role|null first(callable $callback = null, $default = null)
 * @method Role|null last(callable $callback = null, $default = null)
 * @method Role|null pop()
 * @method Role|null shift()
 * @method ArrayIterator|Role[] getIterator()
 */
class RoleEloquentCollection extends Collection
{}

