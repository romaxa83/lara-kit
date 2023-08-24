<?php

namespace App\GraphQL\Mutations\Common\Verification\Phone;

use App\Entities\Messages\ResponseMessageEntity;
use App\GraphQL\Types\Messages\ResponseMessageType;
use App\GraphQL\Types\NonNullType;
use App\Modules\Utils\Phones\Repositories\PhoneRepository;
use App\Modules\Utils\Phones\Services\VerificationService;
use Core\GraphQL\Mutations\BaseMutation;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\SelectFields;

class BaseVerificationMutation extends BaseMutation
{
    public const NAME = 'phoneVerificationMutation';
    public const DESCRIPTION = 'Верификация телефона';

    public function __construct(
        protected PhoneRepository $repo,
        protected VerificationService $service,
    )
    {}

    public function args(): array
    {
        return [
            'token' => [
                'type' => NonNullType::string(),
                'description' => 'Токен, который был выслан при запросе на верификацию'
            ],
            'code' => NonNullType::string(),
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
            $this->service->verify($args['token'], $args['code']);

            return ResponseMessageEntity::success(
                __('messages.phone.phone_verify_success')
            );
        } catch (\Throwable $e) {
            return ResponseMessageEntity::fail($e->getMessage());
        }
    }

    protected function rules(array $args = []): array
    {
        return [
            'code' => ['required'],
            'token' => ['required', 'string'],
        ];
    }
}

