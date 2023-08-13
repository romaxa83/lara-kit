<?php

namespace App\Models\Localization;

use App\Filters\Localization\LocaleFilter;
use App\Traits\Filterable;
use App\Traits\HasFactory;
use App\Traits\QueryCacheable;
use Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $native
 * @property int $sort
 *
 * @see Locale::getMainAttribute()
 * @property-read bool main
 *
 * @method static Builder|self whereId($value)
 * @method static Builder|self whereName($value)
 * @method static Builder|self whereSlug($value)
 */
class Locale extends BaseModel
{
    use Filterable;
    use QueryCacheable;
    use HasFactory;

    public const TABLE = 'locales';

    public const ALLOWED_SORTING_FIELDS = [
        'name',
        'sort',
    ];

    public $timestamps = false;

    protected $table = self::TABLE;

    protected $fillable = [
        'name',
        'slug',
        'native',
        'sort'
    ];

    public function modelFilter(): string
    {
        return LocaleFilter::class;
    }

    public function canBeDeleted(): bool
    {
        return true;
    }

    public function getMainAttribute(): bool
    {
        return $this->pivot?->main ?? false;
    }
}
