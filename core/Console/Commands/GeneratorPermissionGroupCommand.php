<?php

declare(strict_types=1);

namespace Core\Console\Commands;

class GeneratorPermissionGroupCommand extends BaseModuleGeneratorPermissionCommand
{
    protected $name = 'generate:group-permission';
    protected $type = 'Permission group';

    public function handle(): ?bool
    {
        foreach (['create', 'update', 'delete', 'list'] as $action) {
            $this->generateCRUDPermission($action);
        }

        return parent::handle();
    }

    protected function generateCRUDPermission(string $action): void
    {
        $this->call(GenerateCrudPermissionCommand::NAME, [
            'name' => $this->argument('name'),
            '--action' => $action,
            '--module' => $this->option('module'),
            '--submodule' => $this->option('submodule'),
        ]);
    }

    protected function getStub(): string
    {
        return base_path('stubs/permissions/group.stub');
    }

    protected function getClassNameSuffix(): string
    {
        return $this->getPermissionGroupSuffix();
    }
}
