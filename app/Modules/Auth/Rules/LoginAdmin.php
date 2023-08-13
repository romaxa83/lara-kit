<?php

namespace App\Modules\Auth\Rules;

use App\Modules\Admin\Models\Admin;
use App\Modules\Admin\Repositories\AdminRepository;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class LoginAdmin implements Rule
{
    public function __construct(
        protected array $args
    ) {
    }

    public function passes($attribute, $value): bool
    {
        /** @var $repo AdminRepository */
        $repo = resolve(AdminRepository::class);

        /** @var $admin Admin */
        if (!$admin = $repo->getBy('email', $this->args['email'])) {
            return false;
        }

        return Hash::check($this->args['password'], $admin->password);
    }

    public function message(): string
    {
        return __('auth.failed');
    }
}
