<?php

declare(strict_types=1);

namespace App\Traits\GraphQL;

use GraphQL\Type\Definition\Type;

trait QueryTextHelperTrait
{
    protected string $queryTextArgsKey = 'query';

    protected function getQueryTextArgs(): array
    {
        return [
            $this->queryTextArgsKey => Type::string(),
        ];
    }

    protected function getQueryTextRules(): array
    {
        return [
            $this->queryTextArgsKey => ['nullable', 'string', 'min:2'],
        ];
    }
}
