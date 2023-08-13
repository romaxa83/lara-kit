<?php

namespace Core\GraphQL\Mutations;

use App\Entities\Messages\ResponseMessageEntity;
use App\GraphQL\Types\Messages\ResponseMessageType;
use App\GraphQL\Types\NonNullType;
use App\Notifications\Auth\ForgotPasswordVerificationNotification;
use Core\Models\BaseAuthenticatable;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Notification;
use Rebing\GraphQL\Support\SelectFields;

abstract class BaseForgotPasswordMutation extends BaseMutation
{
    public const DESCRIPTION = 'Метод для отправки ссылки для сброса пароля. На почту клиента приходит ссылка в виде {link}/{token}, {link}.';

    public function authorize(
        $root,
        array $args,
        $ctx,
        ResolveInfo $info = null,
        \Closure $fields = null
    ): bool
    {
        return $this->guest();
    }

    public function type(): Type
    {
        return ResponseMessageType::type();
    }

    public function args(): array
    {
        return [
            'email' => [
                'type' => NonNullType::string(),
            ],
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
            /** @var $model BaseAuthenticatable */
            $model = $this->repo->getBy(
                'email',
                $args['email'],
                withException:true,
                exceptionMessage: __('exceptions.not_found_by_email', ['email' => $args['email']])
            );

            Notification::route('mail', $model->email->getValue())
                ->notify(
                    (new ForgotPasswordVerificationNotification(
                        $model,
                        $this->service->getLinkForPasswordReset($model)
                    ))->locale($model->lang)
                );

            return ResponseMessageEntity::success(__('messages.forgot_password.send.success', ['email' => $args['email']]));
        } catch (\Throwable $e) {
            return ResponseMessageEntity::fail($e->getMessage());
        }
    }

    protected function rules(array $args = []): array
    {
        return [
            'email' => 'required|email:filter',
        ];
    }
}

