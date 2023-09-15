<?php

namespace App\Modules\Utils\Phones\Collections;

use App\Modules\Utils\Phones\Models\Phone;
use ArrayIterator;
use Illuminate\Database\Eloquent\Collection;

/**
 * @method Phone|null first(callable $callback = null, $default = null)
 * @method Phone|null last(callable $callback = null, $default = null)
 * @method Phone|null pop()
 * @method Phone|null shift()
 * @method ArrayIterator|Phone[] getIterator()
 */
class PhoneEloquentCollection extends Collection
{}
