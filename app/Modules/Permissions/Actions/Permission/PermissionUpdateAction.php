<?php

namespace App\Modules\Permissions\Actions\Permission;

use App\Modules\Permissions\Dto\PermissionEditDto;
use App\Modules\Permissions\Dto\PermissionTranslationDto;
use App\Modules\Permissions\Models\Permission;
use App\Modules\Permissions\Models\PermissionTranslation;

final class PermissionUpdateAction
{
    public function exec(Permission $model, PermissionEditDto $dto): Permission
    {
        $model->load(['translation']);

        make_transaction(function () use ($model, $dto) {
            foreach ($dto->getTranslations() as $translation){
                /** @var $translation PermissionTranslationDto */
                if(is_support_lang($translation->lang)){

                    if($t = $model->translations->where('lang', $translation->lang)->first()){
                        $t->title = $translation->title;
                    } else {
                        $t = new PermissionTranslation();
                        $t->row_id = $model->id;
                        $t->title = $translation->title;
                        $t->lang = $translation->lang;
                    }

                    $t->save();
                }
            }
        });

        return $model->refresh();
    }
}
