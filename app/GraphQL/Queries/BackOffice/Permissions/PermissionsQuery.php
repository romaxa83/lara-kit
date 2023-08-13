<?php

namespace App\GraphQL\Queries\BackOffice\Permissions;

use App\GraphQL\Types\Enums\Permissions\GuardEnum;
use App\GraphQL\Types\Roles\GrantGroupType;
use App\Modules\Permissions\Enums\Guard;
use Core\GraphQL\Queries\BaseQuery;
use Core\Services\Permissions\PermissionService;
use GraphQL\Type\Definition\{ResolveInfo, Type};
use Rebing\GraphQL\Support\SelectFields;

class PermissionsQuery extends BaseQuery
{
    public const NAME = 'permissions';

    public function __construct(
        protected PermissionService $permissionService
    )
    {
        $this->setAdminGuard();
    }

    public function args(): array
    {
        return array_merge(
            [
                'guard' => GuardEnum::type(),
            ]
        );
    }

    public function type(): Type
    {
        return GrantGroupType::list();
    }

    public function doResolve(
        mixed $root,
        array $args,
        mixed $context,
        ResolveInfo $info,
        SelectFields $fields
    ): array
    {
        return $this->permissionService
            ->getGroupsFor($args['guard'])
            ->toArray();
    }

    protected function rules(array $args = []): array
    {
        return [
            'guard' => ['required', 'string' , Guard::ruleIn()],
        ];
    }
}
