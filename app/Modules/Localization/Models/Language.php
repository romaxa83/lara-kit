<?php

namespace App\Modules\Localization\Models;

use App\Modules\Localization\Filters\LanguageFilter;
use App\Traits\Filterable;
use App\Traits\HasFactory;
use App\Traits\QueryCacheable;
use Core\Models\BaseModel;
use Core\Traits\Models\ActiveTrait;
use Core\Traits\Models\DefaultTrait;
use Database\Factories\Localization\LanguageFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Modules\Localization\Collections\LanguageEloquentCollection;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $locale
 * @property bool $default Language by default
 * @property bool $active
 * @property int $sort
 *
 * @see Language::getIsCurrentAttribute()
 * @property-read bool is_current
 *
 * @method static Builder|self whereCreatedAt($value)
 * @method static Builder|self whereDefault($value)
 * @method static Builder|self whereId($value)
 * @method static Builder|self whereName($value)
 * @method static Builder|self whereSlug($value)
 * @method static Builder|self whereUpdatedAt($value)
 *
 * @see Language::scopeDefault()
 * @method static Builder|self default()
 *
 * @method static LanguageFactory factory(...$parameters)
 */
class Language extends BaseModel
{
    use HasFactory;
    use QueryCacheable;
    use ActiveTrait;
    use DefaultTrait;
    use Filterable;

    public const TABLE = 'languages';
    protected $table = self::TABLE;

    public $timestamps = false;

    public static array $links = [];

    protected $fillable = [
        'action',
        'default',
    ];
    protected $casts = [
        'default' => 'boolean',
        'active' => 'boolean',
    ];

    public const ALLOWED_SORTING_FIELDS = [
        'sort',
    ];

    public function modelFilter(): string
    {
        return LanguageFilter::class;
    }

    protected static function newFactory(): Factory
    {
        return LanguageFactory::new();
    }

    public function newCollection(array $models = []): LanguageEloquentCollection
    {
        return LanguageEloquentCollection::make($models);
    }
}
