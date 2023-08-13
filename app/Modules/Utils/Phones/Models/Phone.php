<?php

namespace App\Modules\Utils\Phones\Models;

use App\Modules\Utils\Phones\Casts\PhoneCast;
use App\Modules\Utils\Phones\ValueObject\Phone as PhoneObj;
use Carbon\Carbon;
use Core\Models\BaseModel;
use Core\Traits\Models\DefaultTrait;
use Database\Factories\Utils\Phones\PhoneFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int id
 * @property string model_type
 * @property int model_id
 * @property PhoneObj phone
 * @property null|Carbon phone_verified_at
 * @property bool default
 * @property int code
 * @property null|Carbon code_expired_at
 * @property null|string desc
 *
 * @method static PhoneFactory factory(...$parameters)
 */
class Phone extends BaseModel
{
    use HasFactory;
    use DefaultTrait;

    public const TABLE = 'phones';
    protected $table = self::TABLE;

    public $timestamps = false;

    protected $hidden = [];

    protected $casts = [
        'default' => 'boolean',
        'phone' => PhoneCast::class,
    ];

    protected static function newFactory(): Factory
    {
        return PhoneFactory::new();
    }

    public function model(): MorphTo
    {
        return $this->morphTo('model');
    }
}
