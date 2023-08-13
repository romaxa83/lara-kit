<?php

namespace Core\Services\Permissions;

use App\Dto\Permission\RoleDto;
use App\Exceptions\Permissions\RoleForOwnerException;
use App\Modules\Admin\Models\Admin;
use App\Modules\Permissions\Models\Permission as PermissionModel;
use App\Modules\Permissions\Models\Role;
use App\Modules\Permissions\Models\RoleHasPermission;
use App\Modules\Permissions\Models\RoleTranslation;
use Core\Permissions\Permission;
use Core\Permissions\PermissionGroup;
use Illuminate\Support\Collection;

class PermissionService
{
    private Collection $guards;

    public function __construct()
    {
        $this->guards = Collection::make();
    }

    public function createRole(RoleDto $roleDto, string $guard): Role
    {
        $role = new Role();
        $role->name = $roleDto->getName();
        $role->guard_name = $guard;
        $role->save();

        foreach ($roleDto->getTranslates() as $translate) {
            $roleTranslate = new RoleTranslation();
            $roleTranslate->row_id = $role->id;
            $roleTranslate->lang = $translate->getLanguage();
            $roleTranslate->title = $translate->getTitle();
            $roleTranslate->save();
        }

        $permissions = $this->sync($roleDto->getPermissions(), $guard);

        $this->syncPermissionsForRole($role, $permissions, $guard);

        return $role;
    }

    public function sync(array $permissionList, string $guard): array
    {

        $permissionFilteredList = $this->filterByExistsPermissions($permissionList, $guard);

        $permission = array_map(
            static fn(string $permission) => [
                'name' => $permission,
                'guard_name' => $guard,
            ],
            $permissionFilteredList
        );
//dd($permission);
        PermissionModel::query()->upsert($permission, ['name', 'guard_name']);

        return $permissionFilteredList;
    }

    public function filterByExistsPermissions(array $permissionList, string $guard): array
    {
        $availablePermissions = $this->getPermissionsList($guard)
            ->map(fn(Permission $permission) => $permission->getKey())
            ->toArray();

        return array_intersect($availablePermissions, $permissionList);
    }

    /**
     * @param string $guard
     * @return Collection|Permission[]
     */
    public function getPermissionsList(string $guard): Collection|array
    {
        return $this->getGroupsFor($guard)
            ->map(fn(PermissionGroup $group) => $group->getPermissions())
            ->flatten();
    }

    public function getGroupsFor(string $guard): Collection
    {
        if (!$this->guards->has($guard)) {
            $this->guards->put($guard, $this->buildGroupsFor($guard));
        }

        return $this->guards->get($guard);
    }

    protected function buildGroupsFor(string $guard): Collection
    {
        $result = collect();

//        dd(config("grants.matrix.$guard.groups"), $guard);

        foreach (config("grants.matrix.$guard.groups") as $groupClass => $permissionsClasses) {
            $group = $this->createPermissionGroupInstance($groupClass, $permissionsClasses);

            $result->put($group->getKey(), $group);
        }

        return $result;
    }

    public function createPermissionGroupInstance(string $groupClass, array $permissionsClasses): PermissionGroup
    {
        return new $groupClass(
            array_map(static fn(string $className) => new $className(), $permissionsClasses)
        );
    }

    protected function syncPermissionsForRole(Role $role, array $permissions, string $guard): void
    {
        RoleHasPermission::query()
            ->where('role_id', $role->getKey())
            ->delete();

        $importCollection = PermissionModel::query()
            ->whereIn('name', $permissions)
            ->whereGuardName($guard)
            ->pluck('id')
            ->map(static fn(int $id) => [
                'permission_id' => $id,
                'role_id' => $role->getKey(),
            ]);

        $this->upsertRolePermission($importCollection->toArray());
    }

    protected function upsertRolePermission(array $importable): void
    {
        RoleHasPermission::query()
            ->upsert($importable, ['permission_id', 'role_id']);
    }

    public function createPermissionGroupInstanceForGuard(string $groupClass, string $guard): PermissionGroup
    {
        return $this->createPermissionGroupInstance(
            $groupClass,
            $this->getPermissionClassesByGuardAndGroupClass($guard, $groupClass)
        );
    }

    public function getPermissionClassesByGuardAndGroupClass(string $guard, string $groupClass): array
    {
        return config("grants.matrix.$guard.groups." . $groupClass) ?? [];
    }

    public function updateRole(Role $role, RoleDto $roleDto, string $guard): Role
    {
        $role->guard_name = $guard;
        $role->name = $roleDto->getName();
        $role->save();

        foreach ($roleDto->getTranslates() as $translate) {
            /** @var RoleTranslation $roleTranslate */
            $roleTranslate = $role->translates->where('language', $translate->getLanguage())->first();
            $roleTranslate->title = $translate->getTitle();
            $roleTranslate->save();
        }

        $permissions = $this->sync($roleDto->getPermissions(), $guard);

        $this->syncPermissionsForRole($role, $permissions, $guard);

        return $role;
    }

    public function deleteRole(Role $role): bool
    {
        if ($role->isForOwner()) {
            throw new RoleForOwnerException(__('exceptions.roles.cant-delete-role-for-owner'));
        }

        $role->permissions()->detach();
        $role->translates()->delete();
        $role->delete();

        return true;
    }

    public function firstOrCreateSuperAdminRole(): Role
    {
        return $this->getSuperAdminRole()
            ?: $this->createSuperAdminRole();
    }

    public function getSuperAdminRole(): ?Role
    {
        return Role::query()
            ->whereName(config('permission.roles.super_admin'))
            ->whereGuardName(Admin::GUARD)
            ->first();
    }

    public function createSuperAdminRole(): Role
    {
        $superAdminRoleName = config('permission.roles.super_admin');

        $guard = Admin::GUARD;
        $role = Role::query()->firstOrCreate(
            [
                'name' => $superAdminRoleName,
                'guard_name' => $guard,
            ]
        );

        foreach (languages() as $language) {
            $role->translations()->updateOrCreate(
                [
                    'title' => $superAdminRoleName,
                    'lang' => $language->slug,
                ]
            );
        }

        $this->seedAllPermissionsForGuard($guard);
        $this->syncAllPermissionForRoleAndGuard($role);

        return $role;
    }

    public function seedAllPermissionsForGuard(string $guard): void
    {
        $importCollection = $this->getPermissionsList($guard)
            ->map(static fn(Permission $permission) => [
                'name' => $permission->getKey(),
                'guard_name' => $guard,
            ]);

        PermissionModel::query()
            ->upsert($importCollection->toArray(), ['name', 'guard_name']);
    }

    public function syncAllPermissionForRoleAndGuard(Role $role): void
    {
        $importCollection = PermissionModel::query()
            ->whereGuardName($role->guard_name)
            ->pluck('id')
            ->map(static fn(int $id) => [
                'permission_id' => $id,
                'role_id' => $role->getKey(),
            ]);

        $this->upsertRolePermission($importCollection->toArray());
    }
}
