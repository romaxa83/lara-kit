<?php

namespace App\Traits\Permissions;

use App\Modules\Permissions\Enums\BaseRole;
use App\Modules\Permissions\Enums\Guard;
use App\Modules\Permissions\Models\Permission as PermissionModel;
use App\Modules\Permissions\Models\Role;
use Core\Services\Permissions\PermissionFilterService;
use Illuminate\Database\Eloquent\Collection;
use Spatie\Permission\Contracts\Permission;

/**
 * @see HasRoles::getRoleAttribute()
 * @property-read Permission[]|Collection permissions
 *
 * @see HasRoles::getRoleAttribute()
 * @property-read null|Role $role
 *
 * @see HasRoles::roles()
 * @property-read Collection|Role[] $roles
 */
trait HasRoles
{
    use \Spatie\Permission\Traits\HasRoles;

    public function getRoleAttribute(): ?Role
    {
        return $this->roles->first();
    }

    public function getAllPermissions(): \Illuminate\Support\Collection
    {
        $permissions = $this->permissions;

        if ($this->roles) {
            $permissions = $permissions->merge($this->getPermissionsViaRoles());
        }

        return $this->getPermissionFilterService()
            ->filter($this, $permissions)
            ->sort()
            ->values();
    }

    protected function getPermissionFilterService(): PermissionFilterService
    {
        return app(\Core\Services\Permissions\PermissionFilterService::class);
    }

    protected function hasPermissionViaRole(Permission|PermissionModel $permission): bool
    {
        $filteredPermissions = $this->getPermissionFilterService()
            ->filter($this, collect([$permission]));

        if ($filteredPermissions->isEmpty()) {
            return false;
        }

        return $this->hasRole($permission->roles);
    }

    protected function hasDirectPermission(Permission $permission): bool
    {
        return false;
    }

    public function isSuperAdmin(): bool
    {
        return $this->role->name === BaseRole::SUPER_ADMIN;
    }

    public function isAdmin(): bool
    {
        return $this->role->name === BaseRole::ADMIN;
    }

    public function isUser(): bool
    {
        return $this->role->name === BaseRole::USER;
    }
}
