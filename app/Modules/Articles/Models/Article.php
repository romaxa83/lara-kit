<?php

namespace App\Modules\Articles\Models;

use App\Filters\Admins\AdminFilter;
use App\Modules\Articles\Enums\ArticleStatus;
use App\Traits\Filterable;
use Carbon\Carbon;
use Core\Models\BaseModel;
use Core\Traits\Models\HasTranslations;
use Database\Factories\Articles\ArticleFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int id
 * @property int category_id
 * @property ArticleStatus status
 * @property null|Carbon published_at
 * @property Carbon created_at
 * @property Carbon updated_at
 *
 * @method static ArticleFactory factory(...$parameters)
 */
class Article extends BaseModel
{
    use HasFactory;
    use Filterable;
    use HasTranslations;

    public const TABLE = 'articles';
    protected $table = self::TABLE;

    public const ALLOWED_SORTING_FIELDS = [
        'id',
        'created_at',
        'published_at'
    ];

    protected $fillable = [];

    protected $casts = [
        'status' => ArticleStatus::class,
    ];

    public function modelFilter(): string
    {
        return AdminFilter::class;
    }
}
