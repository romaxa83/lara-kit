<?php

namespace App\Modules\User\Collections;

use App\Modules\User\Models\User;
use ArrayIterator;
use Illuminate\Database\Eloquent\Collection;

/**
 * @method User|null first(callable $callback = null, $default = null)
 * @method User|null last(callable $callback = null, $default = null)
 * @method User|null pop()
 * @method User|null shift()
 * @method ArrayIterator|User[] getIterator()
 */
class UserEloquentCollection extends Collection
{}

