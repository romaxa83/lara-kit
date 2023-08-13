<?php

namespace Core\Traits\Models;

use Illuminate\Database\Eloquent\Builder;

/**
 * @property bool active
 *
 * @see DefaultTrait::isDefault()
 * @method isDefault()
 *
 * @see DefaultTrait::scopeActive()
 * @method static default(bool $value = true)
 */
trait DefaultTrait
{
    public function isDefault(): bool
    {
        return $this->default;
    }

    public function scopeDefault(Builder|self $b, bool $value = true): void
    {
        $b->where(static::TABLE . '.default', $value);
    }
}
