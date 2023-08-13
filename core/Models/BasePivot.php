<?php

namespace Core\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @method static Builder|static query()
 *
 * @mixin BaseModel
 */
abstract class BasePivot extends Pivot
{}
