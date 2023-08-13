<?php

namespace App\Modules\Permissions\Actions\Role;

use App\Modules\Permissions\Dto\RoleEditDto;
use App\Modules\Permissions\Dto\RoleTranslationDto;
use App\Modules\Permissions\Models\Role;
use App\Modules\Permissions\Models\RoleTranslation;
use App\Modules\Permissions\Repositories\PermissionRepository;

final class RoleUpdateAction
{
    public function __construct(
        protected readonly PermissionRepository $permissionsRepo
    )
    {}

    public function exec(Role $model, RoleEditDto $dto): Role
    {
        $model->name = $dto->name;

        $model->save();

        foreach ($dto->getTranslations() as $translation){
            /** @var $translation RoleTranslationDto */

            if(is_support_lang($translation->lang)){
                if($t = $model->translations->where('lang', $translation->lang)->first()){
                    $t->title = $translation->title;
                } else {
                    $t = new RoleTranslation();
                    $t->row_id = $model->id;
                    $t->title = $translation->title;
                    $t->lang = $translation->lang;
                }

                $t->save();
            }
        }

        $model->permissions()->detach();
        if(!empty($dto->getPermissions())){
            $perms = $dto->getPermissions();
            if($dto->isPermissionsAsKey()){
                $perms = $this->permissionsRepo->getPermissionsIdByKey($dto->getPermissions());
            }
            $model->permissions()->attach($perms);
        }

        return $model;
    }
}
