<?php

declare(strict_types=1);

namespace Core\Console\Commands;

use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class GenerateCrudPermissionCommand extends BaseModuleGeneratorPermissionCommand
{
    public const NAME = 'make:crud-permission';

    protected $type = 'Crud permission';
    protected $name = self::NAME;

    protected function getClassNameSuffix(): string
    {
        return Str::ucfirst(
            Str::camel($this->getAction() . 'Permission')
        );
    }

    protected function getAction(): string
    {
        return $this->option('action');
    }

    protected function getStub(): string
    {
        return base_path('stubs/permissions/action.stub');
    }

    protected function buildClass($name): string
    {
        $action = $this->getAction();
        $groupClassName = $this->generatePermissionGroupClassName($name);

        $replace = [
            '{{groupClassName}}' => $groupClassName,
            '{{ groupClassName }}' => $groupClassName,

            '{{action}}' => $action,
            '{{ action }}' => $action,
        ];

        return str_replace(
            array_keys($replace),
            array_values($replace),
            parent::buildClass($name)
        );
    }

    protected function getOptions(): array
    {
        return array_merge(
            parent::getOptions(),
            [
                ['action', 'a', InputOption::VALUE_OPTIONAL, 'The action name'],
            ]
        );
    }
}
