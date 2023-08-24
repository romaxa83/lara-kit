<?php

namespace App\GraphQL\Mutations\Common\Verification\Phone;

use App\Entities\Messages\ResponseMessageEntity;
use App\GraphQL\Types\Messages\ResponseMessageType;
use App\GraphQL\Types\NonNullType;
use App\Modules\Utils\Phones\Models\Phone;
use App\Modules\Utils\Phones\Repositories\PhoneRepository;
use App\Modules\Utils\Phones\Rules\PhoneRule;
use App\Modules\Utils\Phones\Services\VerificationService;
use Core\GraphQL\Mutations\BaseMutation;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\SelectFields;

class BaseRequestMutation extends BaseMutation
{
    public const NAME = 'phoneRequestVerificationMutation';
    public const DESCRIPTION = 'Запрос на верификацию телефона(отправка смс с кодом), в поле "message" вернется токен, который нужно будет отправить с кодом введеным пользователем';

    public function __construct(
        protected PhoneRepository $repo,
        protected VerificationService $service,
    )
    {}

    public function args(): array
    {
        return [
            'phone' => NonNullType::string(),
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
            /** @var $model Phone */
            $model = $this->repo->getBy('phone', $args['phone']);

            return ResponseMessageEntity::success($this->service->requestVerify($model));
        } catch (\Throwable $e) {
            return ResponseMessageEntity::fail($e->getMessage());
        }
    }

    protected function rules(array $args = []): array
    {
        return [
            'phone' => ['required', 'string', new PhoneRule()],
        ];
    }
}

