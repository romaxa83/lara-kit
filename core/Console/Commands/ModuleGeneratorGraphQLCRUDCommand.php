<?php

declare(strict_types=1);

namespace Core\Console\Commands;

use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class ModuleGeneratorGraphQLCRUDCommand extends BaseModuleGeneratorCommand
{
    protected $name = 'make:graphql:action';
    protected $type = 'GraphQL action';

    protected function buildClass($name): string
    {
        $modelName = Str::camel(class_basename($name)) . Str::ucfirst($this->getAction());

        $namespace = $this->getDirNamespace();

        $classNamespace = $this->generateFullClassNamespaceByName($name);

        $className = class_basename($classNamespace);

        $permissionClass = $this->option('permission');
        $permissionName = class_basename($permissionClass);

        $serviceClass = $this->option('service');
        $service = class_basename($serviceClass);

        $dtoClass = $this->option('dto');
        $dto = class_basename($dtoClass);

        $typeClass = $this->option('type');
        $type = class_basename($typeClass);

        $modelClass = $this->option('model');
        $model = class_basename($modelClass);

        $replace = [
            '{{class}}' => $className,
            '{{ class }}' => $className,

            '{{name}}' => $modelName,
            '{{ name }}' => $modelName,

            '{{ namespace }}' => $namespace,
            '{{namespace}}' => $namespace,

            '{{model}}' => $model,
            '{{ model }}' => $model,

            '{{ modelNamespace }}' => $modelClass,
            '{{modelNamespace}}' => $modelClass,

            '{{permission}}' => $permissionName,
            '{{ permission }}' => $permissionName,

            '{{ permissionNamespace }}' => $permissionClass,
            '{{permissionNamespace}}' => $permissionClass,

            '{{dto}}' => $dto,
            '{{ dto }}' => $dto,

            '{{ dtoNamespace }}' => $dtoClass,
            '{{dtoNamespace}}' => $dtoClass,

            '{{type}}' => $type,
            '{{ type }}' => $type,

            '{{ typeNamespace }}' => $typeClass,
            '{{typeNamespace}}' => $typeClass,

            '{{service}}' => $service,
            '{{ service }}' => $service,

            '{{ serviceNamespace }}' => $serviceClass,
            '{{serviceNamespace}}' => $serviceClass,
        ];

        return str_replace(
            array_keys($replace),
            array_values($replace),
            $this->files->get($this->getStub())
        );
    }

    protected function getAction(): string
    {
        return $this->option('action');
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
        return 'App\\GraphQL\\Mutations';
    }

    protected function getStub(): string
    {
        return base_path('stubs/graphql/action.stub');
    }

    protected function getClassNameSuffix(): string
    {
        $action = Str::ucfirst($this->getAction());

        return $action . 'Mutation';
    }

    protected function getOptions(): array
    {
        $options = parent::getOptions();

        $options[] = ['schema', null, InputOption::VALUE_REQUIRED, 'The name of the gql schema'];
        $options[] = ['action', 'a', InputOption::VALUE_REQUIRED, 'The name of the crud action'];
        $options[] = ['permission', 'p', InputOption::VALUE_REQUIRED, 'The permission class'];
        $options[] = ['service', 's', InputOption::VALUE_REQUIRED, 'The service class'];
        $options[] = ['type', 't', InputOption::VALUE_REQUIRED, 'The type class'];
        $options[] = ['dto', 'd', InputOption::VALUE_REQUIRED, 'The dto class'];

        return $options;
    }
}
