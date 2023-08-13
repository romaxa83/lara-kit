<?php

namespace App\Modules\User\Actions;

use App\Modules\User\Dto\UserDto;
use App\Modules\User\Models\User;
use App\Modules\Utils\Phones\PhoneService;

final class UserUpdateAction
{
    public function __construct(protected PhoneService $phoneService)
    {}

    public function exec(
        User $model,
        UserDto $dto,
        bool $verifyPhone = false
    ): User
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


