<?php

declare(strict_types=1);

namespace Core\Console\Commands;

use Core\Traits\Console\CommandModuleNameHelperTrait;
use Core\Traits\Console\CommandSchemaNameHelperTrait;
use Core\Traits\Console\CommandSubmoduleNameHelperTrait;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class GeneratorMutationTestCommand extends BaseModuleGeneratorCommand
{
    use CommandModuleNameHelperTrait;
    use CommandSchemaNameHelperTrait;
    use CommandSubmoduleNameHelperTrait;

    protected $name = 'generate:mutation-test';

    protected $type = 'Mutation test';

    protected function buildClass($name): string
    {
        $modelName = class_basename($name);

        $namespace = $this->getDirNamespace();

        $classNamespace = $this->generateFullClassNamespaceByName($name);

        $className = class_basename($classNamespace);

        $mutationNamespace = $this->option('mutationNamespace');
        $mutation = class_basename($mutationNamespace);

        $replace = [
            '{{class}}' => $className,
            '{{ class }}' => $className,

            '{{name}}' => $modelName,
            '{{ name }}' => $modelName,

            '{{ namespace }}' => $namespace,
            '{{namespace}}' => $namespace,

            '{{mutation}}' => $mutation,
            '{{ mutation }}' => $mutation,

            '{{ mutationNamespace }}' => $mutationNamespace,
            '{{mutationNamespace}}' => $mutationNamespace,
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
        return 'Tests\\Feature\\Mutations';
    }

    protected function getStub(): string
    {
        return base_path('stubs/graphql/mutation-test.stub');
    }

    protected function getClassNameSuffix(): string
    {
        return Str::ucfirst($this->option('action')) . 'MutationTest';
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
        $options[] = ['action', null, InputOption::VALUE_REQUIRED, 'Mutation action name'];
        $options[] = ['mutationNamespace', null, InputOption::VALUE_REQUIRED, 'Mutation Namespace'];

        return $options;
    }
}
