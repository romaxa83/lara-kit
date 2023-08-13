<?php

namespace App\Modules\Permissions\Services;

use App\Modules\Permissions\Actions\Permission\PermissionCreateAction;
use App\Modules\Permissions\Actions\Role\RoleCreateAction;
use App\Modules\Permissions\Dto\PermissionDto;
use App\Modules\Permissions\Dto\RoleDto;
use App\Modules\Permissions\Enums\BaseRole;
use App\Modules\Permissions\Enums\Guard;
use App\Modules\Permissions\Models\Permission as PermissionModel;
use App\Modules\Permissions\Models\Role;
use App\Modules\Permissions\Models\RoleHasPermission;
use Core\Permissions\Permission;
use Core\Permissions\PermissionGroup;

class PermissionService
{
    public function __construct()
    {}

    public function seed()
    {
        $permList = [];
        foreach (Guard::list() as $guard){
            if(config("grants.matrix.$guard.groups")){
                foreach (config("grants.matrix.$guard.groups") as $group => $perm){
                    $group = $this->createPermissionGroupInstance($group, $perm);
                    $permList[$guard][] = $group;
                }
            }
        }

        foreach ($permList as $guard => $perms){
            $tmp[$guard] = [];
            foreach ($perms as $data){
                /** @var $data PermissionGroup */
                foreach ($data->getPermissions() as $permission){
                    /** @var $permission Permission */

                    $tmp[$guard][] = $permission->getKey();
                    if(!PermissionModel::query()->where('guard_name', $guard)->where('name', $permission->getKey())->exists()){
                        $args = [
                            'name' => $permission->getKey(),
                            'guard' => $guard,
                        ];
                        foreach (app_languages() as $lang => $name){
                            $args['translations'][] = [
                                'lang' => $lang,
                                'title' => $data->getName() . ' ' . $permission->getName()
                            ];
                        }

                        /** @var $handler PermissionCreateAction */
                        $handler = resolve(PermissionCreateAction::class);
                        $model = $handler->exec(PermissionDto::byArgs($args));

                        if($role = Role::query()->where('guard_name', $guard)->first()){
                            $role->permissions()->attach($model);
                        }
                    }
                }
            }
        }

        // удаляем те пермишены, которые уже не доступны для данного гварда
        foreach ($tmp as $g => $ps){
            PermissionModel::query()
                ->where('guard_name', $g)
                ->whereNotIn('name', $ps)
                ->delete();
        }
    }

    public function createPermissionGroupInstance(string $groupClass, array $permissionsClasses): PermissionGroup
    {
        return new $groupClass(
            array_map(static fn(string $className) => new $className(), $permissionsClasses)
        );
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

    public function createBaseRole($role)
    {
        if(!Role::query()->where('name', $role)->exists()){

            $guard = BaseRole::getGuardByRole($role);

            $data = [
                'name' => $role,
                'guard' => $guard,
                'permissions' => \App\Modules\Permissions\Models\Permission::query()->where('guard_name', $guard)->get()->pluck('id')->toArray(),
            ];

            foreach (app_languages() as $lang => $name){
                $data['translations'][] = [
                    'lang' => $lang,
                    'title' => ucfirst(remove_underscore($role)),
                ];
            }

            /** @var $handler RoleCreateAction */
            $handler = resolve(RoleCreateAction::class);
            $handler->exec(RoleDto::byArgs($data));
        }
    }

    protected function upsertRolePermission(array $importable): void
    {
        RoleHasPermission::query()
            ->upsert($importable, ['permission_id', 'role_id']);
    }
}

