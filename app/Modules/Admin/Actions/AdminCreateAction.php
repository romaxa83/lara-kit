<?php

namespace App\Modules\Admin\Actions;

use App\Modules\Admin\Dto\AdminDto;
use App\Modules\Admin\Events\AdminCreatedEvent;
use App\Modules\Admin\Models\Admin;
use App\Modules\Utils\Phones\PhoneService;

final class AdminCreateAction
{
    public function __construct(protected PhoneService $phoneService)
    {}

    public function exec(
        AdminDto $dto,
        bool $verifyPhone = false
    ): Admin
    {
        return make_transaction(function() use ($dto, $verifyPhone) {

            $model = new Admin();
            $model->name = $dto->name;
            $model->email = $dto->email;
            $model->setPassword($dto->password);
            $model->lang = $dto->lang ?? default_lang()->slug;

            $model->save();

            $model->assignRole($dto->role);

            if($dto->phone){
                $this->phoneService->create($model, $dto->phone, $verifyPhone);
            }

            event(new AdminCreatedEvent($model, $dto));

            return $model;
        });
    }
}
