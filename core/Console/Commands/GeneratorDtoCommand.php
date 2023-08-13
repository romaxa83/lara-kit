<?php

declare(strict_types=1);

namespace Core\Console\Commands;

class GeneratorDtoCommand extends BaseModuleGeneratorCommand
{
    protected $name = 'generate:dto';
    protected $type = 'Dto';

    protected function getBaseNamespace(): string
    {
        return 'App\\Dto';
    }

    protected function getClassNameSuffix(): string
    {
        return 'Dto';
    }

    protected function buildClass($name): string
    {
        $namespace = $this->getDirNamespace();

        $classNamespace = $this->generateFullClassNamespaceByName($name);

        $className = class_basename($classNamespace);

        $inputObjectName = $this->getInputObjectName();

        $replace = [
            '{{class}}' => $className,
            '{{ class }}' => $className,

            '{{inputName}}' => $inputObjectName,
            '{{ inputName }}' => $inputObjectName,

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
        return base_path('stubs/dto.stub');
    }

    /**
     * @return string
     */
    protected function getInputObjectName(): string
    {
        return $this->getNameInput() . 'Input';
    }
}
