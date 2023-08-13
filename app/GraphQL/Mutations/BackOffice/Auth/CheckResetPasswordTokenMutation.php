<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations\BackOffice\Auth;

use App\GraphQL\Types\NonNullType;
use App\Modules\Admin\Repositories\AdminRepository;
use App\Modules\Utils\Tokenizer\Exceptions\TokenDecryptException;
use App\Traits\Auth\CryptToken;
use Carbon\CarbonImmutable;
use Closure;
use Core\Exceptions\TranslatedException;
use Core\GraphQL\Mutations\BaseMutation;
use Exception;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\SelectFields;

class CheckResetPasswordTokenMutation extends BaseMutation
{
    use CryptToken;

    public const NAME = 'checkResetPasswordToken';
    public const DESCRIPTION = 'Метод для проверки токена при сбросе пароля.';

    public function __construct(
        protected AdminRepository $adminRepository,
    )
    {}

    public function type(): Type
    {
        return Type::boolean();
    }

    public function args(): array
    {
        return [
            'token' => [
                'type' => NonNullType::string(),
            ],
        ];
    }

    public function authorize(
        $root,
        array $args,
        $ctx,
        ResolveInfo $info = null,
        Closure $fields = null): bool
    {
        return $this->guest();
    }

    /**
     * @throws Exception
     */
    public function doResolve(
        mixed $root,
        array $args,
        mixed $context,
        ResolveInfo $info,
        SelectFields $fields
    ): bool
    {
        try {
            $tokenEntity = $this->decryptToken($args['token']);

            $time = CarbonImmutable::createFromTimestamp($tokenEntity->time)
                ->addMinutes(config('auth.reset_password_token_life'));
            if($time < CarbonImmutable::now()){
                return false;
            }

            $user = $this->adminRepository->getBy('id', $tokenEntity->id);

            if(!$user){
                return false;
            }

            return $user->email_verification_code == $tokenEntity->code;

        } catch (TokenDecryptException $e) {
            return false;
        } catch (Exception $e) {
            throw new TranslatedException($e->getMessage());
        }
    }
}
