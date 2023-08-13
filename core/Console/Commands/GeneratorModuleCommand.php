<?php

declare(strict_types=1);

namespace Core\Console\Commands;

use Core\Traits\Console\CommandModuleNameHelperTrait;
use Core\Traits\Console\CommandSubmoduleNameHelperTrait;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class GeneratorModuleCommand extends GeneratorCommand
{
    use CommandModuleNameHelperTrait;
    use CommandSubmoduleNameHelperTrait;

    protected $name = 'generate:module';

    public function handle(): int
    {
        if ($this->option('all')) {
            $this->input->setOption('model', true);
            $this->input->setOption('factory', true);
            $this->input->setOption('filter', true);
            $this->input->setOption('create', true);
            $this->input->setOption('update', true);
            $this->input->setOption('delete', true);
            $this->input->setOption('query', true);
            $this->input->setOption('dto', true);
            $this->input->setOption('type', true);
            $this->input->setOption('permissions', true);
            $this->input->setOption('service', true);
            $this->input->setOption('rule', true);
            $this->input->setOption('query-test', true);
            $this->input->setOption('mutation-test', true);
        }

        $name = $this->getNameInput();

        if ($this->option('model')) {
            $this->call(GeneratorModelCommand::class, [
                'name' => $name,
                '--factory' => true,
                '--migration' => true,
                '--module' => $this->getModuleName(),
                '--factoryNamespace' => $this->getFactoryNamespace(),
                '--filterNamespace' => $this->getFilterNamespace(),
            ]);
        }

        if ($this->option('filter')) {
            $this->call(GenerateModelFilterCommand::class, [
                'name' => $name,
                '--module' => $this->getModuleName(),
            ]);
        }

        if ($this->option('dto')) {
            $this->call(GeneratorDtoCommand::class, [
                'name' => $name,
                '--module' => $this->getModuleName(),
            ]);
        }

        if ($this->option('permissions')) {
            $this->call(GeneratorPermissionGroupCommand::class, [
                'name' => $name,
                '--module' => $this->getModuleName(),
                '--submodule' => $this->getSubmoduleName(),
            ]);
        }

        if ($this->option('rule')) {
            $this->call(GeneratorExistsRuleCommand::class, [
                'name' => $name,
                '--module' => $this->getModuleName(),
            ]);
        }

        if ($this->option('service')) {
            $this->call(GeneratorServiceCommand::class, [
                'name' => $name,
                '--model' => $this->getModelNamespace(),
                '--dto' => $this->getDtoNamespace(),
                '--module' => $this->getModuleName(),
            ]);
        }

        if ($this->option('type')) {
            $this->call(ModuleGeneratorGraphQLTypeCommand::class, [
                'name' => $name,
                '--model' => $this->getModelNamespace(),
                '--module' => $this->getModuleName(),
            ]);
        }

        if ($this->option('query')) {
            $this->call(ModuleGeneratorGraphQLQueryCommand::class, [
                'name' => $name,
                '--type' => $this->getGQLTypeNamespace(),
                '--schema' => $this->getSchema(),
                '--permission' => $this->getListPermissionNamespace(),
                '--model' => $this->getModelNamespace(),
                '--module' => $this->getModuleName(),
            ]);
        }

        if ($this->option('create')) {
            $this->call(ModuleGeneratorGraphQLCRUDCommand::class, [
                'name' => $name,
                '--action' => 'create',
                '--type' => $this->getGQLTypeNamespace(),
                '--schema' => $this->getSchema(),
                '--permission' => $this->getCreatePermissionNamespace(),
                '--dto' => $this->getDtoNamespace(),
                '--model' => $this->getModelNamespace(),
                '--service' => $this->getServiceNamespace(),
                '--module' => $this->getModuleName(),
                '--submodule' => $this->getSubmoduleName(),
            ]);
        }

        if ($this->option('update')) {
            $this->call(ModuleGeneratorGraphQLCRUDCommand::class, [
                'name' => $name,
                '--action' => 'update',
                '--type' => $this->getGQLTypeNamespace(),
                '--schema' => $this->getSchema(),
                '--permission' => $this->getUpdatePermissionNamespace(),
                '--dto' => $this->getDtoNamespace(),
                '--model' => $this->getModelNamespace(),
                '--service' => $this->getServiceNamespace(),
                '--module' => $this->getModuleName(),
                '--submodule' => $this->getSubmoduleName(),
            ]);
        }

        if ($this->option('delete')) {
            $this->call(ModuleGeneratorGraphQLDeleteCommand::class, [
                'name' => $name,
                '--type' => $this->getGQLTypeNamespace(),
                '--schema' => $this->getSchema(),
                '--permission' => $this->getDeletePermissionNamespace(),
                '--dto' => $this->getDtoNamespace(),
                '--rule' => $this->getRuleNamespace(),
                '--model' => $this->getModelNamespace(),
                '--service' => $this->getServiceNamespace(),
                '--module' => $this->getModuleName(),
                '--submodule' => $this->getSubmoduleName(),
            ]);
        }

        if ($this->option('query-test')) {
            $this->call(GeneratorQueryTestCommand::class, [
                'name' => $name,
                '--schema' => $this->getSchema(),
                '--queryNamespace' => $this->getQueryNamespace(),
                '--module' => $this->getModuleName(),
                '--submodule' => $this->getSubmoduleName(),
            ]);
        }

        if ($this->option('mutation-test')) {
            foreach (['create', 'update', 'delete'] as $action) {
                $this->call(GeneratorMutationTestCommand::class, [
                    'name' => $name,
                    '--action' => $action,
                    '--schema' => $this->getSchema(),
                    '--mutationNamespace' => $this->getMutationNamespace($action),
                    '--module' => $this->getModuleName(),
                    '--submodule' => $this->getSubmoduleName(),
                ]);
            }
        }

        return self::SUCCESS;
    }

    private function getFactoryNamespace(): string
    {
        return 'Database\\Factories\\'
            . $this->getModuleName()
            . '\\'
            . $this->getNameInputUcFirst() . 'Factory';
    }

    /**
     * @return string
     */
    private function getNameInputUcFirst(): string
    {
        return Str::ucfirst($this->getNameInput());
    }

    private function getFilterNamespace(): string
    {
        return 'App\\Filters\\'
            . $this->getModuleName()
            . '\\'
            . $this->getNameInputUcFirst() . 'Filter';
    }

    private function getModelNamespace(): string
    {
        return 'App\\Models\\'
            . $this->getModuleName()
            . '\\'
            . $this->getNameInputUcFirst();
    }

    private function getDtoNamespace(): string
    {
        return 'App\\Dto\\'
            . $this->getModuleName()
            . "\\"
            . $this->getNameInputUcFirst() . 'Dto';
    }

    private function getGQLTypeNamespace(): string
    {
        return 'App\\GraphQL\\Types\\'
            . $this->getModuleName()
            . '\\'
            . $this->getNameInputUcFirst()
            . 'Type';
    }

    private function getSchema(): string
    {
        return $this->option('schema');
    }

    private function getListPermissionNamespace(): string
    {
        return $this->generatePermissionNamespace('List');
    }

    private function generatePermissionNamespace(string $action): string
    {
        $result = 'App\\Permissions\\'
            . $this->getModuleName()
            . '\\';

        if ($submodule = $this->getSubmoduleName()) {
            $result .= $submodule . '\\';
        }

        return $result
            . $this->getNameInputUcFirst()
            . $action . 'Permission';
    }

    private function getCreatePermissionNamespace(): string
    {
        return $this->generatePermissionNamespace('Create');
    }

    private function getServiceNamespace(): string
    {
        return 'App\\Services\\'
            . $this->getModuleName()
            . '\\'
            . $this->getNameInputUcFirst()
            . 'Service';
    }

    private function getUpdatePermissionNamespace(): string
    {
        return $this->generatePermissionNamespace('Update');
    }

    private function getDeletePermissionNamespace(): string
    {
        return $this->generatePermissionNamespace('Delete');
    }

    private function getRuleNamespace(): string
    {
        return 'App\\Rules\\ExistsRules\\'
            . $this->getModuleName()
            . '\\'
            . $this->getNameInputUcFirst()
            . 'ExistsRule';
    }

    private function getQueryNamespace(): string
    {
        return 'App\\GraphQL\\Queries\\'
            . $this->getSchema()
            . '\\'
            . $this->getModuleName()
            . '\\'
            . $this->getNameInputUcFirst()
            . 'Query';
    }

    private function getMutationNamespace(string $action): string
    {
        $result = 'App\\GraphQL\\Mutations\\'
            . $this->getSchema()
            . '\\'
            . $this->getModuleName()
            . '\\';

        if ($submodule = $this->getSubmoduleName()) {
            $result .= $submodule . '\\';
        }

        return $result
            . $this->getNameInputUcFirst()
            . Str::ucfirst($action)
            . 'Mutation';
    }

    protected function getOptions(): array
    {
        return [
            ['all', 'a', InputOption::VALUE_NONE, 'Создать все возможные ресурсы для сущности'],
            ['model', 'e', InputOption::VALUE_NONE, 'Создать модель сущности'],
            ['factory', 'f', InputOption::VALUE_NONE, 'Создать фабрику'],
            ['filter', null, InputOption::VALUE_NONE, 'Создать фильтр'],
            ['migration', 'm', InputOption::VALUE_NONE, 'Создать миграции'],
            ['create', 'c', InputOption::VALUE_NONE, 'Мутация создания.'],
            ['update', 'u', InputOption::VALUE_NONE, 'Мутация обновления.'],
            ['delete', 'd', InputOption::VALUE_NONE, 'Мутация удаления.'],
            ['query', null, InputOption::VALUE_NONE, 'Список.'],
            ['query-test', null, InputOption::VALUE_NONE, 'Создать тест списка.'],
            ['mutation-test', null, InputOption::VALUE_NONE, 'Создать тест действий.'],
            ['dto', 'i', InputOption::VALUE_NONE, 'Создать объект передачи данных.'],
            ['type', null, InputOption::VALUE_NONE, 'Создать GQL тип.'],
            ['permissions', 'p', InputOption::VALUE_NONE, 'Создать классы пермишенов.'],
            ['service', 's', InputOption::VALUE_NONE, 'Создать класс сервиса.'],
            ['rule', null, InputOption::VALUE_NONE, 'Создать класс сервиса.'],
            ['module', null, InputOption::VALUE_REQUIRED, 'Название модуля'],
            ['submodule', null, InputOption::VALUE_OPTIONAL, 'Название подмодуля'],
            ['schema', null, InputOption::VALUE_REQUIRED, 'Название схемы gql'],
        ];
    }

    protected function getStub()
    {
    }
}
