<?php

namespace Tests\Traits;

use App\Modules\Admin\Models\Admin;
use App\Modules\Permissions\Enums\Guard;
use App\Modules\Permissions\Models\Role;
use App\Modules\User\Models\User;
use Tests\Builders\Permissions\RoleBuilder;

trait InteractsWithAuth
{
    protected function loginAsUser(User $model = null): User
    {
        if (!$model) {
            $model = $this->userBuilder->create();
        }

        $this->actingAs($model, GUARD::USER);

        return $model;
    }
    protected function loginAsAdmin(Admin $model = null, ...$permissions): Admin
    {
        if (!$model) {
            $model = $this->adminBuilder
                ->permissions(...$permissions)->create();
        }

        $this->actingAs($model, GUARD::ADMIN);

        return $model;
    }

    protected function loginAsSuperAdmin(Admin $model = null): Admin
    {
        if (!$model) {
            $model = $this->adminBuilder
                ->asSuperAdmin()->create();
        }

        if (!$model->isSuperAdmin()) {
            /** @var $role Role */
            $role = resolve(RoleBuilder::class)
                ->asSuperAdmin()->create();

            $model->assignRole($role);
        }

        $this->actingAs($model, GUARD::ADMIN);

        return $model;
    }
}

