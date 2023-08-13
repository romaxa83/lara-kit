<?php

declare(strict_types=1);

namespace Core\Traits\Console;

use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

trait CommandModuleNameHelperTrait
{
    protected function getModuleNameOptionRules(): array
    {
        return ['module', null, InputOption::VALUE_REQUIRED, 'Module name'];
    }

    protected function getModuleName(): string
    {
        return Str::ucfirst(
            Str::plural(
                $this->option('module')
            )
        );
    }
}
