<?php

namespace App\GraphQL\Queries\BackOffice\Admins;

use App\GraphQL\Types\Admins\AdminType;
use App\Modules\Admin\Models\Admin;
use App\Modules\Admin\Repositories\AdminRepository;
use App\Permissions\Admins\AdminListPermission;
use Core\GraphQL\Queries\BaseQuery;
use GraphQL\Type\Definition\{ResolveInfo, Type};
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Rebing\GraphQL\Support\SelectFields;

class AdminsQuery extends BaseQuery
{
    public const NAME = 'admins';
    public const PERMISSION = AdminListPermission::KEY;

    public function __construct(
        protected AdminRepository $repo
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
                'without_id' => Type::id(),
                'query' => Type::string(),
                'phone' => Type::string(),
            ]
        );
    }

    public function type(): Type
    {
        return AdminType::paginate();
    }

    public function doResolve(
        mixed $root,
        array $args,
        mixed $context,
        ResolveInfo $info,
        SelectFields $fields
    ): LengthAwarePaginator
    {
        $superAdmin = $this->repo->getSuperAdmin(['id'], true);
        if($superAdmin){
            $args += ['without_id' => $superAdmin->id];
        }

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
                'query' => ['nullable', 'string'],
                'phone' => ['nullable', 'string'],
            ]
        );
    }

    protected function allowedForSortFields(): array
    {
        return Admin::ALLOWED_SORTING_FIELDS;
    }
}
