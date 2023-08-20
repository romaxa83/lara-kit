<?php

namespace App\Modules\User\Actions;

use App\Modules\Permissions\Enums\BaseRole;
use App\Modules\Permissions\Models\Role;
use App\Modules\Permissions\Repositories\RoleRepository;
use App\Modules\User\Dto\UserDto;
use App\Modules\User\Models\User;
use App\Modules\Utils\Phones\Services\PhoneService;

final class UserCreateAction
{
    public function __construct(
        protected PhoneService $phoneService,
        protected RoleRepository $roleRepository,
    )
    {}

    public function exec(
        UserDto $dto,
        bool $verifyPhone = false
    ): User
    {
        return make_transaction(function() use ($dto, $verifyPhone) {

            /** @var $role Role */
            $role = $this->roleRepository->getBy('name', BaseRole::USER, withException: true,
                exceptionMessage: __('exceptions.role.not_found_user_role')
            );

            $model = new User();
            $model->name = $dto->name;
            $model->email = $dto->email;
            $model->setPassword($dto->password);
            $model->lang = $dto->lang ?? default_lang()->slug;

            $model->save();

            $model->assignRole($role);

            if($dto->phone){
                $this->phoneService->create($model, $dto->phone, $verifyPhone);
            }

            return $model;
        });
    }
}
