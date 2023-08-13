<?php

namespace App\GraphQL\Queries\FrontOffice\Users;

use App\GraphQL\Types\Users\UserProfileType;
use App\Modules\User\Models\User;
use Closure;
use Core\GraphQL\Queries\BaseQuery;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\SelectFields;

class UserProfileQuery extends BaseQuery
{
    public const NAME = 'userProfile';

    public function args(): array
    {
        return [];
    }

    public function authorize($root, array $args, $ctx, ResolveInfo $info = null, Closure $fields = null): bool
    {
        return $this->authCheck();
    }

    public function type(): Type
    {
        return UserProfileType::type();
    }

    public function doResolve(
        $root,
        array $args,
        $context,
        ResolveInfo $info,
        SelectFields $fields
    ): User
    {
        return $this->user()
            ->load($fields->getRelations());
    }
}
