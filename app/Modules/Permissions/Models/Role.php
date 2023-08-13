<?php

namespace App\Modules\Permissions\Models;

use App\Models\ListPermission;
use App\Modules\Permissions\Collections\RoleEloquentCollection;
use App\Modules\Permissions\Enums\Guard;
use App\Modules\Permissions\Filters\RoleFilter;
use App\Traits\Filterable;
use App\Traits\HasFactory;
use App\Traits\Permissions\DefaultListPermissionTrait;
use Core\Models\BaseModel;
use Core\Traits\Models\HasTranslations;
use Database\Factories\Permissions\RoleFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @property int id
 * @property Guard guard_name
 * @property string name
 * @property string created_at
 * @property string updated_at
 * @property Collection|Permission[] permissions
 *
 * @see Role::translations()
 * @property Collection|RoleTranslation[] translations
 *
 * @see Role::translation()
 * @property RoleTranslation translation
 *
 * @property-read string[] permissionList
 * @see Role::getPermissionListAttribute()
 *
 * @method static static|Builder query()
 * @method Builder|static select(...$attrs)
 *
 * @see Role::scopeForUsers()
 * @method Builder|static forUsers()
 *
 * @see Role::scopeDefaultForOwner()
 * @method Builder|static defaultForOwner()
 *
 * @method Builder|static whereName(string $name)
 * @method Builder|static whereGuardName(string $name)
 *
 * @method static RoleFactory factory()
 *
 * @mixin BaseModel
 */
class Role extends \Spatie\Permission\Models\Role implements ListPermission
{
    use Filterable;
    use HasFactory;
    use HasTranslations;
    use DefaultListPermissionTrait;

    public const ALLOWED_SORTING_FIELDS = [
        'id',
        'created_at',
        'updated_at',
        'name',
        'title',
    ];

    public const TABLE = 'roles';

    protected $fillable = [
        'name',
        'guard_name',
    ];
    protected $casts = [
        'guard_name' => Guard::class,
    ];

    protected static function newFactory(): Factory
    {
        return RoleFactory::new();
    }

    public function modelFilter(): string
    {
        return RoleFilter::class;
    }

    public function newCollection(array $models = []): RoleEloquentCollection
    {
        return RoleEloquentCollection::make($models);
    }

    public function getPermissionListAttribute(): array
    {
        return $this->permissions
            ->pluck('name')
            ->toArray();
    }
}
