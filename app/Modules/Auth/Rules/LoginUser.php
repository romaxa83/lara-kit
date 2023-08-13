<?php

namespace App\Modules\Auth\Rules;

use App\Modules\User\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginUser extends LoginAdmin
{
    public function passes($attribute, $value): bool
    {
        if (!$user = User::query()->where('email', $this->args['email'])->first()) {
            return false;
        }

        return Hash::check($this->args['password'], $user->password);
    }
}
