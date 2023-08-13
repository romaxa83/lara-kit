<?php

namespace App\GraphQL\Mutations\BackOffice\Admins;

use App\Modules\Admin\Actions\AdminChangePasswordAction;
use Core\GraphQL\Mutations\BaseChangePasswordMutation;

class AdminChangePasswordMutation extends BaseChangePasswordMutation
{
    public const NAME = 'adminChangePassword';

    public function __construct(protected AdminChangePasswordAction $action)
    {
        $this->setAdminGuard();
    }
}
