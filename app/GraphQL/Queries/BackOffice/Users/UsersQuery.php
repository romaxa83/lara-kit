<?php

namespace App\GraphQL\Queries\BackOffice\Users;

use App\GraphQL\Types\Users\UserType;
use App\Modules\User\Models\User;
use App\Modules\User\Repositories\UserRepository;
use App\Permissions\Users\UserListPermission;
use Core\GraphQL\Queries\BaseQuery;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Rebing\GraphQL\Support\SelectFields;

class UsersQuery extends BaseQuery
{
    public const NAME = 'users';
    public const PERMISSION = UserListPermission::KEY;

    public function __construct(protected UserRepository $repo)
    {
        $this->setAdminGuard();
    }

    public function args(): array
    {
        return array_merge(
            $this->paginationArgs(),
            $this->sortArgs(),
            $this->trashedArgs(),
            [
                'id' => Type::id(),
                'ids' => Type::listOf(Type::id()),
                'query' => Type::string(),
                'phone' => Type::string(),
                'wit' => Type::string(),
            ]
        );
    }

    public function type(): Type
    {
        return UserType::paginate();
    }

    public function doResolve(
        $root,
        array $args,
        $context,
        ResolveInfo $info,
        SelectFields $fields)
    : LengthAwarePaginator
    {
//        dd($args);
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
            $this->trashedRules(),
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
        return User::ALLOWED_SORTING_FIELDS;
    }
}
