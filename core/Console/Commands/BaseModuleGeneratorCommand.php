<?php

declare(strict_types=1);

namespace Core\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

abstract class BaseModuleGeneratorCommand extends GeneratorCommand
{
    protected function getPath($name): string
    {
        $classBaseName = class_basename($name);

        $classNamespace = $this->generateFullClassNamespaceByName($classBaseName);

        $name = Str::replaceFirst($this->rootNamespace(), '', $classNamespace);

        return $this->laravel['path'] . '/' . str_replace('\\', '/', $name) . '.php';
    }

    protected function generateFullClassNamespaceByName(string $name): string
    {
        return $this->getDirNamespace() . '\\' . $this->generateClassName($name);
    }

    protected function getDirNamespace(string $baseNamespace = null): string
    {
        $classNamespace = $baseNamespace ?: $this->getBaseNamespace();

        if ($module = $this->option('module')) {
            $classNamespace .= '\\' . Str::plural($module);
        }

        if ($submodule = $this->option('submodule')) {
            $classNamespace .= '\\' . Str::plural($submodule);
        }

        return $classNamespace;
    }

    abstract protected function getBaseNamespace(): string;

    protected function generateClassName(string $name): string
    {
        $classBaseName = class_basename($name);

        return $classBaseName . $this->getClassNameSuffix();
    }

    abstract protected function getClassNameSuffix(): string;

    protected function generateModelNamespace(string $name): string
    {
        return $this->getDirNamespace("App\\Models") . "\\" . class_basename($name);
    }

    abstract protected function getStub(): string;

    protected function getOptions(): array
    {
        return [
            ['model', 'm', InputOption::VALUE_REQUIRED, 'The name of the model'],
            ['module', null, InputOption::VALUE_OPTIONAL, 'The name of the module'],
            ['submodule', null, InputOption::VALUE_OPTIONAL, 'The name of the submodule'],
        ];
    }
}
