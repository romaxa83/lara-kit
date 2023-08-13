<?php


namespace App\GraphQL\Types\Unions;


use App\GraphQL\Types\Admins\AdminType;
use App\GraphQL\Types\BaseUnionType;
use App\GraphQL\Types\Users\UserType;
use App\Models\Admins\Admin;
use App\Modules\User\Models\User;
use Exception;
use GraphQL\Type\Definition\Type;

class Authenticatable extends BaseUnionType
{
    public const NAME = 'Authenticatable';

    public function types(): array
    {
        return [
            AdminType::type(),
            UserType::type()
        ];
    }

    /**
     * @throws Exception
     */
    public function resolveType(User|Admin $value): Type
    {
        if ($value instanceof User) {
            return UserType::type();
        }

        if ($value instanceof Admin) {
            return AdminType::type();
        }

        throw new Exception(__('exceptions.type_not_found'));
    }
}
