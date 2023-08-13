<?php

namespace Core\Traits\GraphQL\Queries;

use GraphQL\Type\Definition\Type;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Arr;
use Rebing\GraphQL\Support\Facades\GraphQL;

trait PaginateHelperTrait
{
    protected function paginationArgs(): array
    {
        return [
            'per_page' => [
                'type' => Type::int(),
                'description' => 'Maximum value ' . config('queries.default.pagination.max_per_page')
            ],
            'page' => [
                'type' => Type::int(),
            ],
        ];
    }

    protected function paginationRules(): array
    {
        return [
            'per_page' => ['nullable', 'integer'],
            'page' => ['nullable', 'integer'],
        ];
    }

    protected function paginate(Builder|QueryBuilder|Relation $builder, array $args): LengthAwarePaginator
    {
        return $builder->paginate(...$this->getPaginationParameters($args));
    }

    protected function getPaginationParameters(array $args): array
    {
        return [
            $this->getPerPage($args),
            ['*'],
            'page',
            $this->getPage($args)
        ];
    }

    protected function getPerPage(array $args, int $default = null): int
    {
        if (is_null($default)) {
            $default = config('queries.default.pagination.per_page');
        }
        $perPage = Arr::get($args, 'per_page', 0);
        $maxPerPage = config('queries.default.pagination.max_per_page');

        $perPage = min($perPage, $maxPerPage);

        return isset($args['per_page']) ? $perPage : $default;
    }

    protected function getPage(array $args): int
    {
        return $args['page'] ?? 1;
    }

    protected function paginateType(Type $type): Type
    {
        return GraphQL::paginate($type);
    }
}
