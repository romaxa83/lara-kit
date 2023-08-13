<?php

declare(strict_types=1);

namespace Core\GraphQL\Mutations;

use App\Entities\Messages\ResponseMessageEntity;
use App\GraphQL\InputTypes\Auth\ResetPasswordInput;
use App\GraphQL\Types\Messages\ResponseMessageType;
use App\Modules\Admin\Models\Admin;
use App\Modules\Auth\Services\VerificationService;
use App\Modules\User\Models\User;
use App\Modules\Utils\Tokenizer\Tokenizer;
use App\Notifications\Auth\ResetPasswordVerificationNotification;
use App\Traits\Auth\EmailCryptToken;
use Closure;
use Core\Rules\PasswordRule;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Notification;
use Rebing\GraphQL\Support\SelectFields;
use Throwable;

class BaseResetPasswordMutation extends BaseMutation
{
    use EmailCryptToken;

    public const NAME = 'resetPassword';
    public const DESCRIPTION = 'Метод принимает токен, ранее отправленный на почту в виде ссылки на фронт.';

    public function __construct(
        protected VerificationService $verificationService,
    )
    {}

    public function args(): array
    {
        return [
            'input' => ResetPasswordInput::nonNullType(),
        ];
    }

    public function type(): Type
    {
        return ResponseMessageType::type();
    }

    public function authorize(
        $root,
        array $args,
        $ctx,
        ResolveInfo $info = null,
        Closure $fields = null
    ): bool
    {
        return $this->guest();
    }

    /**
     * @throws Throwable
     */
    public function doResolve(
        mixed $root,
        array $args,
        mixed $context,
        ResolveInfo $info,
        SelectFields $fields
    ): ResponseMessageEntity
    {
        try {
            make_transaction(function () use ($args) {
                /** @var $user Admin|User */
                $decrypt = Tokenizer::decryptToken($args['input']['token']);

                $user = $decrypt->modelClass::find($decrypt->modelId);

                $newPassword = $args['input']['password'];

                $user->setPassword($newPassword);
                $user->save();

                $this->verificationService->cleanEmailVerificationCode($user);

                Notification::route('mail', $user->email->getValue())
                    ->notify(
                        (new ResetPasswordVerificationNotification(
                            $user,
                            $newPassword
                        ))
                            ->locale($user->lang)
                    );
            });

            return ResponseMessageEntity::success(__('messages.reset_password.action.success'));
        } catch (\Throwable $e) {
            return ResponseMessageEntity::fail($e->getMessage());
        }
    }

    protected function rules(array $args = []): array
    {
        return [
            'input.token' => ['required', 'string'],
            'input.password' => ['required', 'string', new PasswordRule(), 'confirmed'],
        ];
    }
}
