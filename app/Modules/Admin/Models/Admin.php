<?php

namespace App\Modules\Admin\Models;

use App\Casts\EmailCast;
use App\Models\ListPermission;
use App\Modules\Admin\Collections\AdminEloquentCollection;
use App\Modules\Admin\Filters\AdminFilter;
use App\Modules\Localization\Contracts\Languageable;
use App\Modules\Localization\Traits\HasLanguage;
use App\Modules\Permissions\Enums\Guard;
use App\Modules\Utils\Phones\Contracts\Phoneable;
use App\Modules\Utils\Phones\Models\Phone;
use App\Modules\Utils\Phones\Traits\HasPhone;
use App\Traits\Filterable;
use App\Traits\Permissions\DefaultListPermissionTrait;
use App\Traits\Permissions\HasRoles;
use App\Traits\SetPasswordTrait;
use App\ValueObjects\Email;
use Carbon\Carbon;
use Core\Models\BaseAuthenticatable;
use Core\WebSocket\Contracts\Subscribable;
use Database\Factories\Admins\AdminFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
 * @method Builder|static whereEmail($email)
 * @method static AdminFactory factory(...$parameters)
 */
class Admin extends BaseAuthenticatable implements
    Languageable,
    ListPermission,
    Subscribable,
    Phoneable
{
    use HasFactory;
    use Filterable;
    use Notifiable;
    use HasRoles;
    use HasLanguage;
    use HasPhone;
    use SetPasswordTrait;
    use DefaultListPermissionTrait;
    use SoftDeletes;

    public const GUARD = Guard::ADMIN;
    public const MORPH_NAME = 'admin';

    public const TABLE = 'admins';
    protected $table = self::TABLE;

    public const ALLOWED_SORTING_FIELDS = [
        'id',
        'name',
        'email',
        'created_at'
    ];

    protected $fillable = [];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'email' => EmailCast::class,
    ];

    protected static function newFactory(): Factory
    {
        return AdminFactory::new();
    }

    public function modelFilter(): string
    {
        return AdminFilter::class;
    }

    public function newCollection(array $models = []): AdminEloquentCollection
    {
        return AdminEloquentCollection::make($models);
    }

    public function getEmailVerificationCode(): ?string
    {
        return $this->email_verification_code;
    }

    public function isEmailVerified(): bool
    {
        return $this->email_verified_at !== null;
    }

    public function getUniqId(): string
    {
        return $this->getMorphClass() . '.' . $this->getKey();
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
