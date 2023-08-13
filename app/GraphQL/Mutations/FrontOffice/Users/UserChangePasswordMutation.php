<?php

namespace App\GraphQL\Mutations\FrontOffice\Users;

use App\Modules\User\Actions\UserChangePasswordAction;
use Core\GraphQL\Mutations\BaseChangePasswordMutation;

class UserChangePasswordMutation extends BaseChangePasswordMutation
{
    public const NAME = 'userChangePassword';

    public function __construct(protected UserChangePasswordAction $action)
    {}
}
