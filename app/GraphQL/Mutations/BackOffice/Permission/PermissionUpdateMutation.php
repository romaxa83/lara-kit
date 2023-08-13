<?php

namespace App\GraphQL\Mutations\BackOffice\Permission;

use App\GraphQL\InputTypes\Permissions\PermissionUpdateInput;
use App\GraphQL\Types\NonNullType;
use App\GraphQL\Types\Roles\PermissionType;
use App\Modules\Permissions\Actions\Permission\PermissionUpdateAction;
use App\Modules\Permissions\Dto\PermissionEditDto;
use App\Modules\Permissions\Models\Permission;
use App\Modules\Permissions\Repositories\PermissionRepository;
use App\Permissions\Roles\RoleUpdatePermission;
use Core\GraphQL\Mutations\BaseMutation;
use Core\Rules\TranslatesArrayValidator;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\SelectFields;

class PermissionUpdateMutation extends BaseMutation
{
    public const NAME = 'permissionUpdate';
    public const PERMISSION = RoleUpdatePermission::KEY;

    public function __construct(
        protected PermissionUpdateAction $action,
        protected PermissionRepository $repo
    )
    {
        $this->setAdminGuard();
    }

    public function args(): array
    {
        return [
            'id' => NonNullType::id(),
            'input' => PermissionUpdateInput::type(),
        ];
    }

    public function type(): Type
    {
        return PermissionType::type();
    }

    public function doResolve(
        $root,
        array $args,
        $context,
        ResolveInfo $info,
        SelectFields $fields
    ): Permission
    {
        /** @var $model Permission */
        $model = $this->repo->getBy('id', $args['id'], ['translations']);

        return $this->action->exec(
            $model,
            PermissionEditDto::byArgs($args['input'])
        );
    }

    protected function rules(array $args = []): array
    {
        return [
            'id' => ['required', 'integer', Rule::exists(Permission::TABLE, 'id')],
            'input.translations' => [new TranslatesArrayValidator()],
            'input.translations.*.title' => ['required', 'string', 'min:3'],
        ];
    }
}
