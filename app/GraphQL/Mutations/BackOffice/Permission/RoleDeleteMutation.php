<?php

namespace App\GraphQL\Mutations\BackOffice\Permission;

use App\Entities\Messages\ResponseMessageEntity;
use App\GraphQL\Types\Messages\ResponseMessageType;
use App\GraphQL\Types\NonNullType;
use App\Modules\Permissions\Actions\Role\RoleDeleteAction;
use App\Modules\Permissions\Models\Role;
use App\Permissions\Roles\RoleDeletePermission;
use Core\GraphQL\Mutations\BaseMutation;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\SelectFields;

class RoleDeleteMutation extends BaseMutation
{
    public const NAME = 'roleDelete';
    public const PERMISSION = RoleDeletePermission::KEY;

    public function __construct(
        protected RoleDeleteAction $action
    )
    {
        $this->setAdminGuard();
    }

    public function args(): array
    {
        return [
            'ids' => NonNullType::listOf(NonNullType::id()),
        ];
    }

    public function type(): Type
    {
        return ResponseMessageType::type();
    }

    public function doResolve(
        $root,
        array $args,
        $context,
        ResolveInfo $info,
        SelectFields $fields
    ): ResponseMessageEntity
    {
        try {
            $msg = count($args['ids']) > 1
                ? __('messages.role.actions.delete.success.many_entity')
                : __('messages.role.actions.delete.success.one_entity');


            if(!$this->action->exec($args['ids'])){
                return ResponseMessageEntity::fail(__('Oops, something went wrong!')) ;
            }

            return ResponseMessageEntity::success($msg);
        } catch (\Throwable $e) {
            return ResponseMessageEntity::fail($e->getMessage());
        }
    }

    protected function rules(array $args = []): array
    {
        return [
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer', Rule::exists(Role::TABLE, 'id')],
        ];
    }
}

