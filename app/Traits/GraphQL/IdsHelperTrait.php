<?php

declare(strict_types=1);

namespace App\Traits\GraphQL;

use GraphQL\Type\Definition\Type;
use Illuminate\Contracts\Validation\Rule;

trait IdsHelperTrait
{
    protected string $idsArgsKey = 'ids';

    protected function getIdsArgs(): array
    {
        return [
            $this->idsArgsKey => Type::listOf(
                Type::id()
            ),
        ];
    }

    protected function getIdsRules(Rule $rule = null): array
    {
        $rules = ['nullable', 'integer'];

        if ($rule) {
            $rules[] = $rule;
        }

        return [
            $this->idsArgsKey => ['nullable', 'array'],
            $this->idsArgsKey . '.*' => $rules,
        ];
    }
}
