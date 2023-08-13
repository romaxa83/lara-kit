<?php

namespace App\Modules\Articles\Models;

use App\Traits\HasFactory;
use Core\Models\BaseTranslation;
use Database\Factories\Articles\CategoryTranslationFactory;

/**
 * @property int id
 * @property string title
 * @property null|string description
 * @property int row_id
 * @property string lang
 *
 * @method static CategoryTranslationFactory factory(...$parameters)
 */
class CategoryTranslation extends BaseTranslation
{
    use HasFactory;

    public const TABLE = 'article_category_translations';
    protected $table = self::TABLE;

    public $timestamps = false;

    protected $fillable = [
        'title',
        'description',
        'row_id',
        'lang',
    ];
}
