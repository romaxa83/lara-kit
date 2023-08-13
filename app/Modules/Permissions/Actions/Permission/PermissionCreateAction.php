<?php

namespace App\Modules\Permissions\Actions\Permission;

use App\Modules\Permissions\Dto\PermissionDto;
use App\Modules\Permissions\Dto\PermissionTranslationDto;
use App\Modules\Permissions\Models\Permission;
use App\Modules\Permissions\Models\PermissionTranslation;

final class PermissionCreateAction
{
    public function exec(PermissionDto $dto): Permission
    {
        $model = new Permission();
        $model->name = $dto->name;
        $model->guard_name = $dto->guard;

        $model->save();

        foreach ($dto->getTranslations() as $translation){
            /** @var $translation PermissionTranslationDto */
            if(is_support_lang($translation->lang)){
                $t = new PermissionTranslation();
                $t->row_id = $model->id;
                $t->title = $translation->title;
                $t->lang = $translation->lang;
                $t->save();
            }
        }

        return $model;
    }
}
