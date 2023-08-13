<?php

declare(strict_types=1);

namespace Core\Traits\Console;

use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

trait CommandSchemaNameHelperTrait
{
    protected function getSchemaNameOptionRules(): array
    {
        return ['schema', null, InputOption::VALUE_REQUIRED, 'Schema name'];
    }

    protected function getSchemaName(): string
    {
        return Str::ucfirst(
            $this->option('schema')
        );
    }
}
