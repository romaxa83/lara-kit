<?php

declare(strict_types=1);

namespace Core\Console\Commands;

class GeneratorExistsRuleCommand extends BaseModuleGeneratorCommand
{
    protected $name = 'generate:exists-rule';
    protected $type = 'Exists rule';

    protected function getBaseNamespace(): string
    {
        return 'App\\Rules\\ExistsRules';
    }

    protected function getClassNameSuffix(): string
    {
        return 'ExistsRule';
    }

    protected function buildClass($name): string
    {
        $modelName = class_basename($name);

        $namespace = $this->getDirNamespace();

        $classNamespace = $this->generateFullClassNamespaceByName($name);

        $className = class_basename($classNamespace);

        $modelNamespace = $this->generateModelNamespace($name);

        $replace = [
            '{{class}}' => $className,
            '{{ class }}' => $className,

            '{{name}}' => $modelName,
            '{{ name }}' => $modelName,

            '{{model}}' => $modelName,
            '{{ model }}' => $modelName,

            '{{ modelNamespace }}' => $modelNamespace,
            '{{modelNamespace}}' => $modelNamespace,

            '{{ namespace }}' => $namespace,
            '{{namespace}}' => $namespace,
        ];

        return str_replace(
            array_keys($replace),
            array_values($replace),
            $this->files->get($this->getStub())
        );
    }

    protected function getStub(): string
    {
        return base_path('stubs/exists-rule.stub');
    }
}
