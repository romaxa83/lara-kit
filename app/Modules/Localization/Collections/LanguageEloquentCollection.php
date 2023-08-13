<?php

namespace App\Modules\Localization\Collections;

use App\Modules\Localization\Models\Language;
use ArrayIterator;
use Illuminate\Database\Eloquent\Collection;

/**
 * @method Language|null first(callable $callback = null, $default = null)
 * @method Language|null last(callable $callback = null, $default = null)
 * @method Language|null pop()
 * @method Language|null shift()
 * @method ArrayIterator|Language[] getIterator()
 */
class LanguageEloquentCollection extends Collection
{}

