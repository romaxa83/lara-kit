<?php

namespace Tests\Builders\Permissions;

use App\Modules\Permissions\Enums\BaseRole;
use App\Modules\Permissions\Enums\Guard;
use App\Modules\Permissions\Models\Permission;
use App\Modules\Permissions\Models\Role;
use App\Modules\Permissions\Models\RoleTranslation;
use Tests\Builders\BaseBuilder;

class RoleBuilder extends BaseBuilder
{
    protected array $perms = [];

    function modelClass(): string
    {
        return Role::class;
    }

    protected function getModelTranslationClass(): string
    {
        return RoleTranslation::class;
    }

    public function name(string $value): self
    {
        $this->data['name'] = $value;
        return $this;
    }

    public function guard(Guard|string $value): self
    {
        $this->data['guard_name'] = $value;
        return $this;
    }

    public function permissions(Permission ...$values): self
    {
        foreach ($values as $value) {
            $this->perms[] = $value->id;
        }
        return $this;
    }

    public function asSuperAdmin(): self
    {
        $this->data['name'] = BaseRole::SUPER_ADMIN;
        $this->data['guard_name'] = Guard::ADMIN;
        return $this;
    }

    public function asAdmin(): self
    {
        $this->data['name'] = BaseRole::ADMIN;
        $this->data['guard_name'] = Guard::ADMIN;
        return $this;
    }

    public function asUser(): self
    {
        $this->data['name'] = BaseRole::USER;
        $this->data['guard_name'] = Guard::USER;
        return $this;
    }

    protected function afterSave($model): void
    {
        /** @var $model Role */
        $model->permissions()->attach($this->perms);
    }

    protected function afterClear(): void
    {
        $this->perms = [];
    }
}

