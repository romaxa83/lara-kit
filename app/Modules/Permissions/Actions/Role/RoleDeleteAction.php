<?php

namespace App\Modules\Permissions\Actions\Role;

use App\Modules\Permissions\Collections\RoleEloquentCollection;
use App\Modules\Permissions\Exceptions\PermissionsException;
use App\Modules\Permissions\Models\Role;
use App\Modules\Permissions\Repositories\RoleRepository;

final class RoleDeleteAction
{
    public function __construct(
        protected readonly RoleRepository $repo
    )
    {}

    public function exec(int|array|Role|RoleEloquentCollection $value): bool
    {
        if($value instanceof Role){
            return $this->removeOne($value);
        }
        if(is_numeric($value)){
            return $this->removeOne(
                $this->repo->getBy('id', $value)
            );
        }
        if(is_array($value)) {
            return $this->removeMany(
                $this->repo->getAllByFields(['id' => $value])
            );
        }

        return $this->removeMany($value);
    }

    private function removeOne(Role $model): bool
    {
        $this->assertRole($model);

        return $model->delete();
    }

    private function removeMany(RoleEloquentCollection $collection): bool
    {
        $res = make_transaction(
            fn() => $collection->map(function (Role $m) {
                $this->assertRole($m);

                return $m->delete();
            })
        );

        return !$res->contains(false);
    }

    private function assertRole(Role $model): void
    {
        if($model->users->isNotEmpty()){
            throw new PermissionsException(__('exceptions.role.not_cant_delete_role'));
        }
    }
}

