<?php

namespace App\Modules\Admin\Actions;

use App\Modules\Admin\Models\Admin;

final class AdminChangePasswordAction
{
    public function __construct()
    {}

    public function exec(
        Admin $model,
        string $password,
    ): Admin
    {
        $model->setPassword($password, true);

        return $model->refresh();
    }
}
