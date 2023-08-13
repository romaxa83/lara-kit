<?php

namespace App\GraphQL\Mutations\BackOffice\Admins;

use App\Entities\Messages\ResponseMessageEntity;
use App\Exceptions\Admin\CantDeleteByMyselfException;
use App\GraphQL\Types\Messages\ResponseMessageType;
use App\GraphQL\Types\NonNullType;
use App\Modules\Admin\Actions\AdminDeleteAction;
use App\Modules\Admin\Collections\AdminEloquentCollection;
use App\Modules\Admin\Models\Admin;
use App\Modules\Admin\Repositories\AdminRepository;
use App\Permissions\Admins\AdminDeletePermission;
use Core\GraphQL\Mutations\BaseMutation;
use Exception;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\SelectFields;
use Throwable;

class AdminDeleteMutation extends BaseMutation
{
    public const NAME = 'adminDelete';
    public const PERMISSION = AdminDeletePermission::KEY;

    public function __construct(
        protected AdminRepository $repo,
        protected AdminDeleteAction $action,
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

    /**
     * @throws Throwable
     */
    public function doResolve(
        $root,
        array $args,
        $context,
        ResolveInfo $info,
        SelectFields $fields
    ): ResponseMessageEntity
    {
        try {
            $superAdminID = $this->user()->isSuperAdmin()
                ? $this->user()->id
                : $this->repo->getSuperAdmin(['id'])?->id
            ;
            foreach ($args['ids'] as $id) {
                if ($this->authId() === (int)$id) {
                    throw new CantDeleteByMyselfException(
                        __('messages.admin.actions.delete.fail.reasons.by_myself')
                    );
                }
                if($superAdminID && $superAdminID === (int)$id){
                    throw new Exception(
                        __('messages.admin.actions.delete.fail.reasons.super_admin')
                    );
                }
            }

            $msg = count($args['ids']) > 1
                ? __('messages.admin.actions.delete.success.many_entity')
                : __('messages.admin.actions.delete.success.one_entity');

            /** @var $models AdminEloquentCollection */
            $models = $this->repo->getAllBy(data: $args['ids']);

            if(!$this->action->exec($models)){
               return ResponseMessageEntity::fail(__('Oops, something went wrong!')) ;
            }

            return ResponseMessageEntity::success($msg);
        } catch (Throwable $e) {
            return ResponseMessageEntity::fail($e->getMessage());
        }
    }

    protected function rules(array $args = []): array
    {
        return $this->guest()
            ? []
            : [
                'ids' => ['required', 'array'],
                'ids.*' => ['required', 'integer', Rule::exists(Admin::TABLE, 'id')],
            ];
    }
}
