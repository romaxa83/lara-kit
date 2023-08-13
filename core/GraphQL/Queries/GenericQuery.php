<?php

declare(strict_types=1);

namespace Core\GraphQL\Queries;

use App\GraphQL\Types\BaseType;
use Core\Models\BaseModel;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Rebing\GraphQL\Support\SelectFields;

abstract class GenericQuery extends BaseQuery
{
    protected BaseModel|string $model;
    protected BaseType|string $type;

    public function type(): Type
    {
        return $this->paginateType(
            $this->type::type()
        );
    }

    public function args(): array
    {
        return array_merge(
            $this->paginationArgs(),
            $this->sortArgs(),
            $this->getActiveArgs(),
            $this->getIdsArgs(),
            $this->getQueryTextArgs(),
        );
    }

    public function doResolve(
        mixed $root,
        array $args,
        mixed $context,
        ResolveInfo $info,
        SelectFields $fields
    ): LengthAwarePaginator {
        return $this->paginate(
            $this->model::query()
                ->select($fields->getSelect())
                ->with($fields->getRelations())
                ->filter($args),
            $args
        );
    }

    protected function initArgs(array $args): array
    {
        $args['company_id'] = $this->company()->getKey();

        return $args;
    }

    protected function rules(array $args = []): array
    {
        return $this->returnEmptyIfGuest(
            fn() => array_merge(
                $this->getActiveRules(),
                $this->paginationRules(),
                $this->sortRules(),
                $this->getIdsRules(),
                $this->getQueryTextRules(),
            )
        );
    }

    protected function allowedForSortFields(): array
    {
        return $this->model::getAllowedSortingFields();
    }
}
