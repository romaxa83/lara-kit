<?php

namespace App\GraphQL\Mutations\BackOffice\Permission;

use App\GraphQL\InputTypes\Permissions\RoleCreateInput;
use App\GraphQL\InputTypes\Permissions\RoleUpdateInput;
use App\GraphQL\Types\NonNullType;
use App\GraphQL\Types\Roles\RoleType;
use App\Modules\Permissions\Actions\Role\RoleUpdateAction;
use App\Modules\Permissions\Dto\RoleEditDto;
use App\Modules\Permissions\Models\Role;
use App\Modules\Permissions\Repositories\RoleRepository;
use App\Modules\Permissions\Rules\PermissionsKeyExist;
use App\Modules\Permissions\Rules\RoleUniqueName;
use App\Permissions\Roles\RoleUpdatePermission;
use Core\GraphQL\Mutations\BaseMutation;
use Core\Rules\TranslatesArrayValidator;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\SelectFields;

class RoleUpdateMutation extends BaseMutation
{
    public const NAME = 'roleUpdate';
    public const PERMISSION = RoleUpdatePermission::KEY;

    public ?Role $model = null;

    public function __construct(
        protected RoleUpdateAction $action,
        protected RoleRepository $repo
    )
    {
        $this->setAdminGuard();
    }

    public function args(): array
    {
        return [
            'id' => NonNullType::id(),
            'input' => RoleUpdateInput::type(),
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
            $this->model,
            RoleEditDto::byArgs($args['input'] + ['permissions_as_key' => true])
        );
    }

    protected function rules(array $args = []): array
    {
        $this->model = $this->repo->getBy('id', $args['id'], ['translations']);

        return [
            'id' => ['required', 'integer', Rule::exists(Role::TABLE, 'id')],
            'input.name' => ['required', 'string', 'min:3', new RoleUniqueName($this->model?->guard_name, data_get($args, 'id'))],
            'input.translations' => [new TranslatesArrayValidator()],
            'input.translations.*.title' => ['required', 'string', 'min:3'],
            'input.permissions' => ['array', new PermissionsKeyExist($this->model?->guard_name)],
            'input.permissions.*' => ['required', 'string', 'min:4'],
        ];
    }
}
