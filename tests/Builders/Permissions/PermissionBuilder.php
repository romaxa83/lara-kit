<?php

namespace Tests\Builders\Permissions;

use App\Modules\Permissions\Enums\Guard;
use App\Modules\Permissions\Models\Permission;
use App\Modules\Permissions\Models\PermissionTranslation;
use Tests\Builders\BaseBuilder;

class PermissionBuilder extends BaseBuilder
{
    function modelClass(): string
    {
        return Permission::class;
    }

    protected function getModelTranslationClass(): string
    {
        return PermissionTranslation::class;
    }

    public function name(string $value): self
    {
        $this->data['name'] = $value;
        return $this;
    }

    public function guard(Guard $value): self
    {
        $this->data['guard_name'] = $value;
        return $this;
    }
}
