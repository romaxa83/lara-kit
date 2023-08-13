<?php

namespace App\GraphQL\Queries\BackOffice\Permissions;

use App\GraphQL\Types\Enums\Permissions\GuardEnum;
use App\GraphQL\Types\Roles\RoleType;
use App\Modules\Permissions\Enums\Guard;
use App\Modules\Permissions\Models\Role;
use App\Modules\Permissions\Repositories\RoleRepository;
use App\Permissions\Roles\RoleListPermission;
use Core\GraphQL\Queries\BaseQuery;
use GraphQL\Type\Definition\{ResolveInfo, Type};
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Rebing\GraphQL\Support\SelectFields;

class RolesQuery extends BaseQuery
{
    public const NAME = 'roles';
    public const PERMISSION = RoleListPermission::KEY;

    public function __construct(
        protected RoleRepository $repo
    )
    {
        $this->setAdminGuard();
    }

    public function args(): array
    {
        return array_merge(
            $this->paginationArgs(),
            $this->sortArgs(),
            [
                'id' => Type::id(),
                'ids' => Type::listOf(Type::id()),
                'name' => Type::string(),
                'title' => Type::string(),
                'guard' => GuardEnum::type(),
            ]
        );
    }

    public function type(): Type
    {
        return RoleType::paginate();
    }

    public function doResolve(
        mixed $root,
        array $args,
        mixed $context,
        ResolveInfo $info,
        SelectFields $fields
    ): LengthAwarePaginator
    {
        $superAdminRole = $this->repo->getSuperAdmin(['id'], true);
        $args['without_id'] = $superAdminRole?->id;

        return $this->repo->getPagination(
            $fields->getSelect() ?: ['id'],
            $fields->getRelations(),
            $args
        );
    }

    protected function rules(array $args = []): array
    {
        return array_merge(
            $this->paginationRules(),
            $this->sortRules(),
            [
                'id' => ['nullable', 'int'],
                'ids' => ['nullable', 'array'],
                'ids.*' => ['required', 'int'],
                'name' => ['nullable', 'string'],
                'title' => ['nullable', 'string'],
                'guard' => ['nullable', 'string' , Guard::ruleIn()],
            ]
        );
    }

    protected function allowedForSortFields(): array
    {
        return Role::ALLOWED_SORTING_FIELDS;
    }
}
