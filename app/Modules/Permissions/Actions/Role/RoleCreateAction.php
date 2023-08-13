<?php

namespace App\Modules\Permissions\Actions\Role;

use App\Modules\Permissions\Dto\RoleDto;
use App\Modules\Permissions\Dto\RoleTranslationDto;
use App\Modules\Permissions\Models\Role;
use App\Modules\Permissions\Models\RoleTranslation;
use App\Modules\Permissions\Repositories\PermissionRepository;

final class RoleCreateAction
{
    public function __construct(
        protected readonly PermissionRepository $permissionsRepo
    )
    {}

    public function exec(RoleDto $dto): Role
    {
        return make_transaction(function() use ($dto) {
            $model = new Role();
            $model->name = $dto->name;
            $model->guard_name = $dto->guard;

            $model->save();

            foreach ($dto->getTranslations() as $translation){
                /** @var $translation RoleTranslationDto */
                if(is_support_lang($translation->lang)){
                    $t = new RoleTranslation();
                    $t->row_id = $model->id;
                    $t->title = $translation->title;
                    $t->lang = $translation->lang;
                    $t->save();
                }
            }

            if(!empty($dto->getPermissions())){
                $perms = $dto->getPermissions();
                if($dto->isPermissionsAsKey()){
                    $perms = $this->permissionsRepo->getPermissionsIdByKey($dto->getPermissions());
                }

                $model->permissions()->attach($perms);
            }

            return $model;
        });
    }
}
