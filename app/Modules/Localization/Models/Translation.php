<?php

namespace App\Modules\Localization\Models;

use App\Filters\Localization\TranslateSimpleFilter;
use App\Modules\Localization\Filters\TranslationFilter;
use App\Modules\Localization\Traits\HasLanguage;
use App\Traits\Filterable;
use App\Traits\HasFactory;
use App\Traits\QueryCacheable;
use Carbon\Carbon;
use Core\Models\BaseModel;
use Database\Factories\Localization\TranslationFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @property int id
 * @property string place
 * @property string key
 * @property string text
 * @property string lang
 * @property Carbon created_at
 * @property Carbon updated_at
 *
 * @property-read Language language
 *
 * @method static static|Translation find($id)
 *
 * @method static TranslationFactory factory(...$options)
 */
class Translation extends BaseModel
{
    use HasFactory;
    use Filterable;
    use QueryCacheable;
    use HasLanguage;

    public const TABLE = 'translations';

    protected $table = self::TABLE;

    protected $fillable = [
        'place',
        'key',
        'text',
        'lang'
    ];

    public const ALLOWED_SORTING_FIELDS = [
        'lang',
        'key',
        'place',
    ];

    public function modelFilter(): string
    {
        return $this->provideFilter(TranslationFilter::class);
    }

    protected static function newFactory(): Factory
    {
        return TranslationFactory::new();
    }
}
