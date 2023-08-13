<?php

namespace App\GraphQL\Mutations\BackOffice\Permission;

use App\GraphQL\InputTypes\Permissions\RoleCreateInput;
use App\GraphQL\Types\Roles\RoleType;
use App\Modules\Permissions\Actions\Role\RoleCreateAction;
use App\Modules\Permissions\Dto\RoleDto;
use App\Modules\Permissions\Enums\Guard;
use App\Modules\Permissions\Models\Role;
use App\Modules\Permissions\Rules\PermissionsKeyExist;
use App\Modules\Permissions\Rules\RoleUniqueName;
use App\Permissions\Roles\RoleCreatePermission;
use Core\GraphQL\Mutations\BaseMutation;
use Core\Rules\TranslatesArrayValidator;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\SelectFields;

class RoleCreateMutation extends BaseMutation
{
    public const NAME = 'roleCreate';
    public const PERMISSION = RoleCreatePermission::KEY;

    public function __construct(protected RoleCreateAction $action)
    {
        $this->setAdminGuard();
    }

    public function args(): array
    {
        return [
            'input' => RoleCreateInput::type(),
        ];
    }

    public function type(): Type
    {
        return RoleType::type();
    }

    public function doResolve(
        $root,
        array $args,
        $context,
        ResolveInfo $info,
        SelectFields $fields
    ): Role
    {
        return $this->action->exec(
            RoleDto::byArgs($args['input'] + ['permissions_as_key' => true])
        );
    }

    protected function rules(array $args = []): array
    {
        return [
            'input.name' => ['required', 'string', 'min:3', new RoleUniqueName($args['input']['guard'])],
            'input.guard' => ['required', 'string', Guard::ruleIn()],
            'input.translations' => [new TranslatesArrayValidator()],
            'input.translations.*.title' => ['required', 'string', 'min:3'],
            'input.permissions' => ['array', new PermissionsKeyExist($args['input']['guard'])],
            'input.permissions.*' => ['required', 'string', 'min:4'],
        ];
    }
}
