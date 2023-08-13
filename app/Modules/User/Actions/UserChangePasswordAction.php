<?php

namespace App\Modules\User\Actions;

use App\Modules\User\Models\User;

final class UserChangePasswordAction
{
    public function __construct()
    {}

    public function exec(
        User $model,
        string $password,
    ): User
    {
        $model->setPassword($password, true);

        return $model->refresh();
    }
}
