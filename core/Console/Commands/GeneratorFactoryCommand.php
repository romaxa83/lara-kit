<?php

declare(strict_types=1);

namespace Core\Console\Commands;

use Core\Traits\Console\CommandModuleNameHelperTrait;
use Illuminate\Database\Console\Factories\FactoryMakeCommand;

class GeneratorFactoryCommand extends FactoryMakeCommand
{
    use CommandModuleNameHelperTrait;

    protected $name = 'generate:factory';

    protected function qualifyClass($name): string
    {
        $moduleNamespaceChunk = $this->getModuleName() . '\\';

        if (!str_contains($name, $moduleNamespaceChunk)) {
            $name = $moduleNamespaceChunk . $name;
        }

        return parent::qualifyClass($name);
    }

    protected function getOptions(): array
    {
        $options = parent::getOptions();

        $options[] = $this->getModuleNameOptionRules();

        return $options;
    }
}
