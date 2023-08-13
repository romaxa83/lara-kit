<?php

namespace App\Modules\Permissions\Models;

use App\Traits\HasFactory;
use Core\Models\BaseTranslation;
use Database\Factories\Permissions\RoleTranslationFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Rennokki\QueryCache\Traits\QueryCacheable;

/**
 * @property int id
 * @property string title
 * @property int row_id
 * @property string lang
// * @property-read Language lang
 * @property-read Role row
 *
 * @method static Builder|self query()
 * @method static RoleTranslationFactory factory()
 */
class RoleTranslation extends BaseTranslation
{
    use HasFactory;
    use QueryCacheable;

    public const TABLE = 'role_translations';
    protected $table = self::TABLE;

    public $timestamps = false;

    protected $touches = ['row'];

    protected $fillable = [
        'title',
        'lang'
    ];

    protected $hidden = [
        'row_id',
    ];

    protected static function newFactory(): Factory
    {
        return RoleTranslationFactory::new();
    }
}
