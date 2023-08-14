<?php

namespace Core\Traits\GraphQL\Queries;

use GraphQL\Type\Definition\Type;

trait TrashedHelper
{
    protected function trashedArgs(): array
    {
        return [
            'with_trash' => [
                'type' => Type::boolean(),
                'description' => 'Выдача моделей вместе с удалеными (soft delete)'
            ],
            'only_trash' => [
                'type' => Type::boolean(),
                'description' => 'Выдача только удаленых моделей (soft delete)'
            ],
        ];
    }

    protected function trashedRules(): array
    {
        return [
            'with_trash' => ['nullable', 'boolean'],
            'only_trash' => ['nullable', 'boolean'],
        ];
    }
}
