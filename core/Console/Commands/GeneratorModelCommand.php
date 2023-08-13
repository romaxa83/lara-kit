<?php

declare(strict_types=1);

namespace Core\Console\Commands;

use Core\Traits\Console\CommandModuleNameHelperTrait;
use Illuminate\Foundation\Console\ModelMakeCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class GeneratorModelCommand extends ModelMakeCommand
{
    use CommandModuleNameHelperTrait;

    protected $name = 'generate:model';

    protected function getOptions(): array
    {
        $options = parent::getOptions();

        $options[] = $this->getModuleNameOptionRules();
        $options[] = ['factoryNamespace', null, InputOption::VALUE_REQUIRED, 'Factory namespace'];
        $options[] = ['filterNamespace', null, InputOption::VALUE_REQUIRED, 'Filter namespace'];

        return $options;
    }

    protected function replaceNamespace(&$stub, $name): static
    {
        $searched = ['{{ factoryNamespace }}', '{{ filterNamespace }}'];
        $replaces = [$this->option('factoryNamespace'), $this->option('filterNamespace')];

        $stub = str_replace($searched, $replaces, $stub);

        return parent::replaceNamespace($stub, $name);
    }

    protected function replaceClass($stub, $name): string
    {
        $searched = ['{{ factory }}', '{{ filter }}', '{{ table }}'];
        $replaces = [
            class_basename($this->option('factoryNamespace')),
            class_basename($this->option('filterNamespace')),
            $this->getTableName()
        ];

        $stub = str_replace($searched, $replaces, $stub);

        return parent::replaceClass($stub, $name);
    }

    protected function createFactory(): void
    {
        $name = $this->getNameInput();
        $qualifyClass = $this->qualifyClass($name);

        $this->call('generate:factory', [
            'name' => $name,
            '--model' => $qualifyClass,
            '--module' => $this->getModuleName(),
        ]);
    }

    protected function qualifyClass($name): string
    {
        $moduleNamespaceChunk = $this->getModuleName() . '\\';

        if (!str_contains($name, $moduleNamespaceChunk)) {
            $name = $moduleNamespaceChunk . $name;
        }

        return parent::qualifyClass($name);
    }

    /**
     * @return string
     */
    protected function getTableName(): string
    {
        return Str::snake(Str::plural($this->getNameInput()));
    }
}
