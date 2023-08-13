<?php

declare(strict_types=1);

namespace Core\Console\Commands;

use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class GeneratorServiceCommand extends BaseModuleGeneratorCommand
{
    protected $name = 'make:service';
    protected $type = 'Service';

    protected function buildClass($name): string
    {
        $modelName = Str::camel(class_basename($name));

        $variables = Str::plural($modelName);

        $namespace = $this->getDirNamespace();

        $classNamespace = $this->generateFullClassNamespaceByName($name);

        $className = class_basename($classNamespace);

        $dtoClass = $this->option('dto');
        $dto = class_basename($dtoClass);

        $modelClass = $this->option('model');
        $model = class_basename($modelClass);

        $replace = [
            '{{variable}}' => $modelName,
            '{{ variable }}' => $modelName,

            '{{variables}}' => $variables,
            '{{ variables }}' => $variables,

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

            '{{dto}}' => $dto,
            '{{ dto }}' => $dto,

            '{{ dtoNamespace }}' => $dtoClass,
            '{{dtoNamespace}}' => $dtoClass,
        ];

        return str_replace(
            array_keys($replace),
            array_values($replace),
            $this->files->get($this->getStub())
        );
    }

    protected function getStub(): string
    {
        return base_path('stubs/service.stub');
    }

    protected function getBaseNamespace(): string
    {
        return 'App\\Services';
    }

    protected function getClassNameSuffix(): string
    {
        return 'Service';
    }

    protected function getOptions(): array
    {
        $options = parent::getOptions();

        $options[] = ['dto', 'd', InputOption::VALUE_REQUIRED, 'The dto class'];

        return $options;
    }
}
