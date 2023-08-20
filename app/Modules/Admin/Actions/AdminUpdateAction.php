<?php

namespace App\Modules\Admin\Actions;

use App\Modules\Admin\Dto\AdminDto;
use App\Modules\Admin\Models\Admin;
use App\Modules\Utils\Phones\Services\PhoneService;

final class AdminUpdateAction
{
    public function __construct(protected PhoneService $phoneService)
    {}

    public function exec(
        Admin $model,
        AdminDto $dto,
        bool $verifyPhone = false
    ): Admin
    {
        return make_transaction(function() use ($model, $dto, $verifyPhone) {

            $model->name = $dto->name;

            if(!$model->email->compare($dto->email)){
                $model->email = $dto->email;
                $model->email_verified_at = null;
                $model->email_verification_code = null;
            }

            if ($dto->password) {
                $model->setPassword($dto->password);
            }
            if($dto->lang){
                $model->lang = $dto->lang;
            }
            if ($dto->role) {
                $model->syncRoles($dto->role);
            }

            if($dto->phone){
                $this->phoneService->createOrUpdate($model, $dto->phone, $verifyPhone);
            }

            if ($model->isDirty()) {
                $model->save();
            }

            return $model;
        });
    }
}

