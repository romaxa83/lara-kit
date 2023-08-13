<?php

declare(strict_types=1);

namespace App\Traits\GraphQL;

use GraphQL\Type\Definition\Type;
use Illuminate\Contracts\Validation\Rule;

trait ActiveHelperTrait
{
    protected string $activeArgsKey = 'active';

    protected function getActiveArgs(): array
    {
        return [
            $this->activeArgsKey => Type::boolean(),
        ];
    }

    protected function getActiveRules(Rule $rule = null): array
    {
        $rules = [
            'nullable',
            'boolean',
        ];

        if ($rule) {
            $rules[] = $rule;
        }

        return [
            $this->activeArgsKey => $rules,
        ];
    }
}
