<?php

namespace App\Modules\Permissions\Rules;

use App\Modules\Permissions\Models\Permission;
use App\Modules\User\Models\User;

use Core\Services\Permissions\PermissionService;
use Illuminate\Contracts\Validation\Rule;

class PermissionsKeyExist implements Rule
{
    public function __construct(
        protected string|null $guard
    )
    {}

    public function passes($attribute, $value): bool
    {
        if(!$this->guard) {
            return false;
        }

        return Permission::query()
            ->where('guard_name' , $this->guard)
            ->whereIn('name', $value)
            ->count() === count($value)
            ;
    }

    public function message(): string
    {
        return __('validation.custom.permissions.contain_not_valid');
    }
}

