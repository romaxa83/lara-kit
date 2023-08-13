<?php

declare(strict_types=1);

namespace Core\Console\Commands;

use Core\Traits\Console\CommandModuleNameHelperTrait;
use Core\Traits\Console\CommandSchemaNameHelperTrait;
use Core\Traits\Console\CommandSubmoduleNameHelperTrait;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class GeneratorQueryTestCommand extends BaseModuleGeneratorCommand
{
    use CommandModuleNameHelperTrait;
    use CommandSchemaNameHelperTrait;
    use CommandSubmoduleNameHelperTrait;

    protected $name = 'generate:query-test';

    protected $type = 'Query test';

    protected function buildClass($name): string
    {
        $modelName = class_basename($name);

        $namespace = $this->getDirNamespace();

        $classNamespace = $this->generateFullClassNamespaceByName($name);

        $className = class_basename($classNamespace);

        $queryNamespace = $this->option('queryNamespace');
        $query = class_basename($queryNamespace);

        $replace = [
            '{{class}}' => $className,
            '{{ class }}' => $className,

            '{{name}}' => $modelName,
            '{{ name }}' => $modelName,

            '{{ namespace }}' => $namespace,
            '{{namespace}}' => $namespace,

            '{{query}}' => $query,
            '{{ query }}' => $query,

            '{{ queryNamespace }}' => $queryNamespace,
            '{{queryNamespace}}' => $queryNamespace,
        ];

        return str_replace(
            array_keys($replace),
            array_values($replace),
            $this->files->get($this->getStub())
        );
    }

    protected function getDirNamespace(string $baseNamespace = null): string
    {
        $classNamespace = $baseNamespace ?: $this->getBaseNamespace();

        if ($schema = $this->getSchemaName()) {
            $classNamespace .= '\\' . $schema;
        }

        if ($module = $this->getModuleName()) {
            $classNamespace .= '\\' . Str::plural($module);
        }

        if ($submodule = $this->getSubmoduleName()) {
            $classNamespace .= '\\' . Str::plural($submodule);
        }

        return $classNamespace;
    }

    protected function getBaseNamespace(): string
    {
        return 'Tests\\Feature\\Queries';
    }

    protected function getStub(): string
    {
        return base_path('stubs/graphql/query-test.stub');
    }

    protected function getClassNameSuffix(): string
    {
        return 'QueryTest';
    }

    protected function getPath($name): string
    {
        $name = $this->generateFullClassNamespaceByName($name);

        $name = str_replace('Tests', '', $name);

        return base_path('tests') . str_replace('\\', '/', $name) . '.php';
    }

    protected function getOptions(): array
    {
        $options = parent::getOptions();

        $options[] = $this->getSchemaNameOptionRules();
        $options[] = ['queryNamespace', null, InputOption::VALUE_REQUIRED, 'Query Namespace'];

        return $options;
    }
}
