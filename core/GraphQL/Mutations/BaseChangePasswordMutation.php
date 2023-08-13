<?php

namespace Core\GraphQL\Mutations;

use App\Entities\Messages\ResponseMessageEntity;
use App\GraphQL\InputTypes\Auth\ChangePasswordInput;
use App\GraphQL\Types\Messages\ResponseMessageType;
use Closure;
use Core\Rules\MatchOldPassword;
use Core\Rules\PasswordRule;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\SelectFields;

abstract class BaseChangePasswordMutation extends BaseMutation
{
    public function authorize(
        $root,
        array $args,
        $ctx,
        ResolveInfo $info = null,
        Closure $fields = null
    ): bool
    {
        return $this->getAuthGuard()->check();
    }

    public function type(): Type
    {
        return ResponseMessageType::type();
    }

    public function args(): array
    {
        return [
            'input' => ChangePasswordInput::nonNullType(),
        ];
    }

    public function doResolve(
        mixed $root,
        array $args,
        mixed $context,
        ResolveInfo $info,
        SelectFields $fields
    ): ResponseMessageEntity
    {
        try {
            $this->action->exec(
                $this->getAuthGuard()->user(),
                $args['input']['password']
            );

            return ResponseMessageEntity::success(__('messages.user.actions.change_password.success'));
        } catch (\Throwable $e) {
            return ResponseMessageEntity::fail($e->getMessage());
        }
    }

    protected function rules(array $args = []): array
    {
        if ($this->guest()) {
            return [];
        }

        return [
            'input.current' => ['required', 'string', new MatchOldPassword($this->guard)],
            'input.password' => ['required', 'string', new PasswordRule(), 'confirmed'],
        ];
    }
}
