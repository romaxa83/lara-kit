<?php

namespace App\Modules\Permissions\Models;

use App\Traits\HasFactory;
use Carbon\Carbon;
use Core\Models\BaseModel;
use Core\Traits\Models\HasTranslations;
use Database\Factories\Permissions\PermissionFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @property int id
 * @property string name
 * @property string guard_name
 * @property Carbon|null created_at
 * @property Carbon|null updated_at
 *
 * @see Permission::roles()
 * @property-read Role[]|Collection roles
 *
 * @method Builder|static whereGuardName(string $name)
 *
 * @method static Builder|static query()
 *
 * @method static PermissionFactory factory()
 * @mixin BaseModel
 */
class Permission extends \Spatie\Permission\Models\Permission
{
    use HasFactory;
    use HasTranslations;

    public const TABLE = 'permissions';

    public static function create(array $attributes = []): static
    {
        return parent::create($attributes);
    }

    protected static function newFactory(): Factory
    {
        return PermissionFactory::new();
    }
}
