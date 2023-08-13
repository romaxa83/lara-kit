<?php

namespace App\Modules\Permissions\Rules;

use App\Modules\Permissions\Models\Role;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Eloquent\Builder;

class RoleUniqueName implements Rule
{
    public function __construct(
        protected string|null $guard,
        protected  int|string|null $id = null
    )
    {}

    public function passes($attribute, $value): bool
    {
        if(!$this->guard) {
            return false;
        }

//        dd(Role::query()
//            ->where('name', $value)
//            ->where('guard_name', $this->guard)
//            ->when($this->id,
//                function (Builder $query) {
//                    return $query->where('id', '!=', $this->id);
//                }
//            )
//            ->exists());

        return !Role::query()
            ->where('name', $value)
            ->where('guard_name', $this->guard)
            ->when($this->id,
                function (Builder $query) {
                    return $query->where('id', '!=', $this->id);
                }
            )->exists();
    }

    public function message(): string
    {
        return __('validation.custom.permissions.role_name_not_unique');
    }
}
