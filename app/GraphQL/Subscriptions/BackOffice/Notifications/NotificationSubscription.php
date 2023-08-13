<?php

namespace App\GraphQL\Subscriptions\BackOffice\Notifications;

use App\Entities\Messages\ResponseMessageEntity;
use App\GraphQL\Subscriptions\BackOffice\BaseBackOfficeSubscription;
use App\GraphQL\Types\Messages\ResponseMessageType;
use Core\Enums\Messages\MessageTypeEnum;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\SelectFields;

class NotificationSubscription extends BaseBackOfficeSubscription
{
    public const NAME = 'notification';

    public function type(): Type
    {
        return ResponseMessageType::type();
    }

    public function doResolve(
        mixed $root,
        array $args,
        mixed $context,
        ResolveInfo $info,
        SelectFields $fields
    ): ResponseMessageEntity {
        return new ResponseMessageEntity(
            $context['message'],
            $context['type'] ?? MessageTypeEnum::SUCCESS,
        );
    }
}
