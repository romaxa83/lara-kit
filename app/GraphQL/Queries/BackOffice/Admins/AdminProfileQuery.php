<?php

namespace App\GraphQL\Queries\BackOffice\Admins;

use App\GraphQL\Types\Admins\AdminProfileType;
use App\Modules\Admin\Models\Admin;
use Closure;
use Core\GraphQL\Queries\BaseQuery;
use GraphQL\Type\Definition\{ResolveInfo, Type};
use Rebing\GraphQL\Support\SelectFields;

class AdminProfileQuery extends BaseQuery
{
    public const NAME = 'adminProfile';

    public function __construct()
    {
        $this->setAdminGuard();
    }

    public function authorize(
        mixed $root,
        array $args,
        mixed $ctx,
        ResolveInfo $info = null,
        Closure $fields = null
    ): bool {
        return $this->authCheck();
    }

    public function args(): array
    {
        return [];
    }

    public function type(): Type
    {
        return AdminProfileType::type();
    }

    public function doResolve(
        mixed $root,
        array $args,
        mixed $context,
        ResolveInfo $info,
        SelectFields $fields
    ): Admin
    {
        /** @var $model Admin */
        $model = $this->user();

        return $model->load($fields->getRelations());
    }
}
