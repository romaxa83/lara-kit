<?php

namespace App\GraphQL\Mutations\BackOffice\Users;

use App\Entities\Messages\ResponseMessageEntity;
use App\GraphQL\Types\Messages\ResponseMessageType;
use App\GraphQL\Types\NonNullType;
use App\Modules\User\Actions\UserDeleteAction;
use App\Modules\User\Collections\UserEloquentCollection;
use App\Modules\User\Models\User;
use App\Modules\User\Repositories\UserRepository;
use App\Permissions\Users\UserDeletePermission;
use Core\GraphQL\Mutations\BaseMutation;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\SelectFields;
use Throwable;

class UserDeleteMutation extends BaseMutation
{
    public const NAME = 'usersDelete';
    public const PERMISSION = UserDeletePermission::KEY;

    public function __construct(
        protected UserRepository $repo,
        protected UserDeleteAction $action,
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
            $msg = count($args['ids']) > 1
                ? __('messages.user.actions.delete.success.many_entity')
                : __('messages.user.actions.delete.success.one_entity');

            /** @var $models UserEloquentCollection */
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
        return [
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer', Rule::exists(User::TABLE, 'id')]
        ];
    }
}
