<?php

namespace App\Modules\User\Models;

use App\Casts\EmailCast;
use App\Models\ListPermission;
use App\Modules\Localization\Contracts\Languageable;
use App\Modules\Localization\Traits\HasLanguage;
use App\Modules\Permissions\Enums\Guard;
use App\Modules\User\Collections\UserEloquentCollection;
use App\Modules\User\Filters\UserFilter;
use App\Modules\Utils\Phones\Contracts\Phoneable;
use App\Modules\Utils\Phones\Models\Phone;
use App\Modules\Utils\Phones\Traits\HasPhone;
use App\Traits\Filterable;
use App\Traits\HasFactory;
use App\Traits\Permissions\DefaultListPermissionTrait;
use App\Traits\Permissions\HasRoles;
use App\Traits\SetPasswordTrait;
use App\ValueObjects\Email;
use Carbon\Carbon;
use Core\Models\BaseAuthenticatable;
use Core\WebSocket\Contracts\Subscribable;
use Database\Factories\Users\UserFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

/**
 * @property int id
 * @property string name
 * @property string password
 * @property string lang
 * @property Email email
 * @property Carbon|null email_verified_at
 * @property int|null email_verification_code
 * @property Carbon created_at
 * @property Carbon updated_at
 *
 * @see Admin::phones()
 * @property-read Phone[]|Collection phones
 *
 * @see Admin::phone()
 * @property-read Phone|null phone
 *
 * @method static UserFactory factory(...$options)
 */
class User extends BaseAuthenticatable implements
    Languageable,
    ListPermission,
    Subscribable,
    Phoneable
{
    use HasFactory;
    use HasRoles;
    use HasPhone;
    use HasLanguage;
    use Filterable;
    use Notifiable;
    use SetPasswordTrait;
//    use AddSelectTrait;
    use DefaultListPermissionTrait;
    use SoftDeletes;

    public const GUARD = Guard::USER;

    public const MORPH_NAME = 'user';

    public const TABLE = 'users';
    protected $table = self::TABLE;

    public const ALLOWED_SORTING_FIELDS = [
        'id',
        'name',
        'email',
        'created_at',
    ];

    public const ALLOWED_SORTING_FIELDS_RELATIONS = [
        'roles' => 'roles.translate.title',
    ];

    protected $fillable = [];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime:Y-m-d H:i:s',
        'email' => EmailCast::class,
    ];

    protected static function newFactory(): Factory
    {
        return UserFactory::new();
    }

    public function modelFilter(): string
    {
        return UserFilter::class;
    }

    public function newCollection(array $models = []): UserEloquentCollection
    {
        return UserEloquentCollection::make($models);
    }

    public function getUniqId(): string
    {
        return $this->getMorphClass() . '.' . $this->getKey();
    }

    public function getEmail(): Email
    {
        return $this->email;
    }
    public function isEmailVerified(): bool
    {
        return $this->email_verified_at !== null;
    }

    public function getEmailVerificationCode(): ?string
    {
        return $this->email_verification_code;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
