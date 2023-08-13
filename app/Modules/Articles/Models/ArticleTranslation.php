<?php

namespace App\Modules\Articles\Models;

use App\Traits\HasFactory;
use Core\Models\BaseTranslation;
use Database\Factories\Articles\ArticleTranslationFactory;

/**
 * @property int id
 * @property string title
 * @property string text
 * @property null|string description
 * @property int row_id
 * @property string lang
 *
 * @method static ArticleTranslationFactory factory(...$parameters)
 */
class ArticleTranslation extends BaseTranslation
{
    use HasFactory;

    public const TABLE = 'article_translations';
    protected $table = self::TABLE;

    public $timestamps = false;

    protected $fillable = [
        'title',
        'text',
        'description',
        'row_id',
        'lang',
    ];
}
