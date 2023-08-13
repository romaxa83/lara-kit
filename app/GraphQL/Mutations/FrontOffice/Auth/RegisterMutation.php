<?php

namespace App\GraphQL\Mutations\FrontOffice\Auth;

use App\GraphQL\InputTypes\Users\UserRegisterInput;
use App\GraphQL\Types\Auth\AuthTokenType;
use App\Modules\User\Actions\UserCreateAction;
use App\Modules\User\Dto\UserDto;
use App\Modules\User\Models\User;
use App\Modules\Utils\Phones\Rules\PhoneRule;
use App\Modules\Utils\Phones\Rules\PhoneUniqueRule;
use App\Services\Auth\UserPassportService;
use App\Traits\Auth\CanRememberMe;
use Closure;
use Core\Enums\Messages\AuthorizationMessageEnum;
use Core\GraphQL\Mutations\BaseMutation;
use Core\Rules\NameRule;
use Core\Rules\PasswordRule;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\SelectFields;
use Throwable;

class RegisterMutation extends BaseMutation
{
    use CanRememberMe;

    public const NAME = 'register';

    public function __construct(
        protected readonly UserCreateAction $action,
        private readonly UserPassportService $userPassportService
    )
    {}

    public function authorize(
        $root,
        array $args,
        $ctx,
        ResolveInfo $info = null,
        Closure $fields = null): bool
    {
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

    public function args(): array
    {
        return [
            'input' => UserRegisterInput::nonNullType(),
        ];
    }

    public function validationErrorMessages(array $args = []): array
    {
        return [
            'email.unique' => __('validation.unique_email'),
        ];
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
    ): array
    {
        $dto = UserDto::byArgs($args['input']);
        $this->action->exec($dto);

        $this->setRefreshTokenTtl($args['input']['remember_me']);

        return $this->userPassportService->auth($dto->email, $dto->password);
    }

    protected function rules(array $args = []): array
    {
        return [
                'input.name' => ['required', 'string', new NameRule('name')],
                'input.email' => ['required', 'string', 'email', 'unique:users,email'],
                'input.password' => ['required', 'string', new PasswordRule(), 'confirmed'],
                'input.phone' => ['nullable', 'string', new PhoneRule(), new PhoneUniqueRule(User::class)],
            ] + $this->rememberMeRule('input.');
    }
}
