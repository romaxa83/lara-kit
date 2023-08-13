<?php

declare(strict_types=1);

namespace Core\Console\Commands;

use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class ModuleGeneratorGraphQLQueryCommand extends BaseModuleGeneratorCommand
{
    protected $name = 'generate:graphql:query';
    protected $type = 'GraphQL query';

    protected function buildClass($name): string
    {
        $modelName = class_basename($name);

        $namespace = $this->getDirNamespace();

        $classNamespace = $this->generateFullClassNamespaceByName($name);

        $className = class_basename($classNamespace);

        $modelNamespace = $this->option('model');

        $typeClass = $this->option('type');
        $typeClassShort = class_basename($typeClass);

        $permissionClass = $this->option('permission');
        $permissionClassShort = class_basename($permissionClass);

        $replace = [
            '{{class}}' => $className,
            '{{ class }}' => $className,

            '{{name}}' => $modelName,
            '{{ name }}' => $modelName,

            '{{model}}' => $modelName,
            '{{ model }}' => $modelName,

            '{{ namespacedPermission }}' => $permissionClass,
            '{{namespacedPermission}}' => $permissionClass,

            '{{ permission }}' => $permissionClassShort,
            '{{permission}}' => $permissionClassShort,

            '{{ namespacedType }}' => $typeClass,
            '{{namespacedType}}' => $typeClass,

            '{{ type }}' => $typeClassShort,
            '{{type}}' => $typeClassShort,

            '{{ namespacedModel }}' => $modelNamespace,
            '{{namespacedModel}}' => $modelNamespace,

            '{{ namespace }}' => $namespace,
            '{{namespace}}' => $namespace,
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

        if ($schema = $this->option('schema')) {
            $classNamespace .= '\\' . $schema;
        }

        if ($module = $this->option('module')) {
            $classNamespace .= '\\' . Str::plural($module);
        }

        if ($submodule = $this->option('submodule')) {
            $classNamespace .= '\\' . Str::plural($submodule);
        }

        return $classNamespace;
    }

    protected function getBaseNamespace(): string
    {
        return 'App\\GraphQL\\Queries';
    }

    protected function getStub(): string
    {
        return base_path('stubs/graphql/query.stub');
    }

    protected function getClassNameSuffix(): string
    {
        return 'Query';
    }

    protected function getOptions(): array
    {
        $options = parent::getOptions();
        $options[] = ['schema', null, InputOption::VALUE_REQUIRED, 'Curren gql schema name'];
        $options[] = ['type', 't', InputOption::VALUE_REQUIRED, 'Current gql type class'];
        $options[] = ['permission', 'p', InputOption::VALUE_REQUIRED, 'Current permission class'];

        return $options;
    }
}
