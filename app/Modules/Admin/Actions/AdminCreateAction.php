<?php

namespace App\Modules\Admin\Actions;

use App\Modules\Admin\Dto\AdminDto;
use App\Modules\Admin\Events\AdminCreatedEvent;
use App\Modules\Admin\Models\Admin;
use App\Modules\Utils\Phones\Dto\PhoneDto;
use App\Modules\Utils\Phones\Services\PhoneService;

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

            if($dto->phoneDto){
                $dto->phoneDto->verify = $verifyPhone;
                $this->phoneService->create($model, $dto->phoneDto);
            } elseif ($dto->phonesDto) {
                $dto->phonesDto->verify = $verifyPhone;
                $this->phoneService->creates($model, $dto->phonesDto);
            }

            event(new AdminCreatedEvent($model, $dto));

            return $model->refresh();
        });
    }
}
