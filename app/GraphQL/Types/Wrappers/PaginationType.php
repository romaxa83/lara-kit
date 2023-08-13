<?php

namespace App\GraphQL\Types\Wrappers;

use GraphQL\Type\Definition\Type;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\PaginationType as OriginPaginationType;

class PaginationType extends OriginPaginationType
{
    protected function getPaginationFields(string $typeName): array
    {
        return [
            'data' => [
                'type' => Type::listOf(GraphQL::type($typeName)),
                'description' => 'List of items on the current page',
                'resolve' => fn(LengthAwarePaginator $data): Collection|array => $data->items(),
            ],
            'meta' => [
                'type' => PaginationMeta::nonNullType($typeName),
                'description' => 'Pagination meta data',
                'selectable' => false,
                'resolve' => fn(LengthAwarePaginator $data): LengthAwarePaginator => $data,
            ],
        ];
    }
}
