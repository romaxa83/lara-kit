<?php

namespace Core\GraphQL\Mutations;

use App\GraphQL\Types\Auth\AuthTokenType;
use App\Traits\Auth\CanRememberMe;
use Closure;
use Core\Enums\Messages\AuthorizationMessageEnum;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\SelectFields;

abstract class BaseLoginMutation extends BaseMutation
{
    use CanRememberMe;

    public function authorize(
        mixed $root,
        array $args,
        mixed $ctx,
        ResolveInfo $info = null,
        Closure $fields = null
    ): bool {
        return $this->getAuthGuard()->guest();
    }

    public function getAuthorizationMessage(): string
    {
        return AuthorizationMessageEnum::AUTHORIZED;
    }

    public function type(): Type
    {
        return AuthTokenType::type();
    }

    public function doResolve(
        mixed $root,
        array $args,
        mixed $context,
        ResolveInfo $info,
        SelectFields $fields
    ): array
    {
        $this->setRefreshTokenTtl($args['input']['remember_me']);

        return $this->passportService->auth(
            $args['input']['email'],
            $args['input']['password']
        );
    }
}
