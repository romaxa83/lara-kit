<?php

namespace Core\Services\Permissions\Filters;

use App\Modules\Permissions\Models\Permission;
use Illuminate\Support\Collection;

abstract class BasePermissionFilter implements PermissionFilter
{
    protected function filterPermissions(Collection $permissions): Collection
    {
        $allowedPermissions = array_map(
            static fn(string $permissionClass) => app($permissionClass)->getKey(),
            $this->getAllowedPermissions()
        );

        $allowedPermissionsFlipped = array_flip($allowedPermissions);

        return $permissions->filter(
            fn(Permission $permission) => array_key_exists($permission->name, $allowedPermissionsFlipped)
        );
    }

    protected function getAllowedPermissions(): array
    {
        return config('grants.filters.' . static::class);
    }
}
