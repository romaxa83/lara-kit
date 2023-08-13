<?php

declare(strict_types=1);

namespace Core\Traits\Console;

use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

trait CommandSubmoduleNameHelperTrait
{
    protected function getSubmoduleNameOptionRules(): array
    {
        return ['submodule', null, InputOption::VALUE_OPTIONAL, 'Submodule name'];
    }

    protected function getSubmoduleName(): string
    {
        return Str::ucfirst(
            Str::plural(
                $this->option('submodule')
            )
        );
    }
}
