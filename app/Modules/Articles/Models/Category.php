<?php

namespace App\Modules\Articles\Models;

use Carbon\Carbon;
use Core\Models\BaseModel;
use Core\Traits\Models\HasTranslations;
use Database\Factories\Articles\CategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int id
 * @property int sort
 * @property Carbon created_at
 * @property Carbon updated_at

 * @method static CategoryFactory factory(...$parameters)
 */
class Category extends BaseModel
{
    use HasFactory;
    use HasTranslations;

    public const TABLE = 'article_categories';
    protected $table = self::TABLE;

    public const ALLOWED_SORTING_FIELDS = [
        'id',
        'sort',
        'created_at',
    ];

    protected $fillable = [];
}
