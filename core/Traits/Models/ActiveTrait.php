<?php

namespace Core\Traits\Models;

use Illuminate\Database\Eloquent\Builder;

/**
 * @property bool active
 *
 * @see ActiveTrait::isActive()
 * @method isActive()
 *
 * @see ActiveScopeTrait::scopeActive()
 * @method static active(bool $value = true)
 */
trait ActiveTrait
{
    public function isActive(): bool
    {
        return $this->active;
    }

    public function scopeActive(Builder|self $b, bool $value = true): void
    {
        $b->where(static::TABLE . '.active', $value);
    }
}
