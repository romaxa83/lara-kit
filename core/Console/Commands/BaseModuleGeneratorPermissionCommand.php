<?php

declare(strict_types=1);

namespace Core\Console\Commands;

use Illuminate\Support\Str;

abstract class BaseModuleGeneratorPermissionCommand extends BaseModuleGeneratorCommand
{
    protected function getBaseNamespace(): string
    {
        return "App\\Permissions";
    }

    protected function buildClass($name): string
    {
        $namespace = $this->getDirNamespace();

        $classNamespace = $this->generateFullClassNamespaceByName($name);

        $shortName = class_basename($name);
        $fullClassName = class_basename($classNamespace);

        $nameKey = Str::snake($shortName);

        $replace = [
            '{{className}}' => $fullClassName,
            '{{ className }}' => $fullClassName,

            '{{nameKey}}' => $nameKey,
            '{{ nameKey }}' => $nameKey,

            '{{ namespace }}' => $namespace,
            '{{namespace}}' => $namespace,
        ];

        return str_replace(
            array_keys($replace),
            array_values($replace),
            $this->files->get($this->getStub())
        );
    }

    abstract protected function getStub(): string;

    abstract protected function getClassNameSuffix(): string;

    protected function generatePermissionGroupClassName(string $name): string
    {
        $prefix = class_basename($name);

        return $prefix . $this->getPermissionGroupSuffix();
    }

    protected function getPermissionGroupSuffix(): string
    {
        return 'PermissionGroup';
    }
}
