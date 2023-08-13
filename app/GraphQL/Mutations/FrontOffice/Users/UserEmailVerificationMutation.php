<?php

namespace App\GraphQL\Mutations\FrontOffice\Users;

use App\Services\Users\UserVerificationService;
use Closure;
use Core\GraphQL\Mutations\BaseMutation;
use Exception;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\SelectFields;

class UserEmailVerificationMutation extends BaseMutation
{
    public const NAME = 'userEmailVerification';

    public function __construct(private readonly UserVerificationService $userVerificationService)
    {
    }

    public function type(): Type
    {
        return Type::boolean();
    }

    public function authorize($root, array $args, $ctx, ResolveInfo $info = null, Closure $fields = null): bool
    {
        return $this->getAuthGuard()->check();
    }

    public function args(): array
    {
        return [
            'code' => Type::nonNull(Type::string()),
        ];
    }

    /**
     * @throws Exception
     */
    public function doResolve($root, array $args, $context, ResolveInfo $info, SelectFields $fields): bool
    {
        return $this->userVerificationService->verifyEmailByCode(
            $this->user(),
            $args['code']
        );
    }

    protected function rules(array $args = []): array
    {
        return [
            'code' => ['required', 'string', 'min:6', 'max:6'],
        ];
    }
}
