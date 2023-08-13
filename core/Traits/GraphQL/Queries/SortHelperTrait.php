<?php

namespace Core\Traits\GraphQL\Queries;

use App\Rules\SortParameterRule;
use GraphQL\Type\Definition\Type;

trait SortHelperTrait
{
    protected function sortArgs(): array
    {
        return [
            'sort' => [
                'type' => Type::string(),
                'description' => 'Аргумент сортировки. Доступные поля: ' . $this->allowedForSortFieldsToString(),
            ],
        ];
    }

    protected function allowedForSortFieldsToString(): string
    {
        return implode(', ', $this->allowedForSortFields());
    }

    protected function allowedForSortFields(): array
    {
        return [];
    }

    protected function sortRules(): array
    {
        return [
            'sort' => ['nullable', 'string', new SortParameterRule($this->allowedForSortFields())]
        ];
    }
}
