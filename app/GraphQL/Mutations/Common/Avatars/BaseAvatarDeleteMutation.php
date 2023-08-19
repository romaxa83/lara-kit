<?php

namespace App\GraphQL\Mutations\Common\Avatars;

use App\Entities\Messages\ResponseMessageEntity;
use App\GraphQL\Types\Messages\ResponseMessageType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\SelectFields;

abstract class BaseAvatarDeleteMutation extends BaseAvatarMutation
{
    public const NAME = 'avatarDelete';

    public function args(): array
    {
        return $this->avatarArgs();
    }

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
    ): ResponseMessageEntity
    {
        try {
            $this
                ->resolveModel($args['model_type'], $args['model_id'])
                ->deleteAvatar();

            return ResponseMessageEntity::success(
                __("messages.media.avatar.actions.delete.success")
            );
        } catch (\Throwable $e) {
            return ResponseMessageEntity::fail($e->getMessage());
        }
    }
}

