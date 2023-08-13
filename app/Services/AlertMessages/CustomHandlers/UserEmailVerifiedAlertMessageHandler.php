<?php

namespace App\Services\AlertMessages\CustomHandlers;

use App\Entities\Messages\AlertMessageEntity;
use App\Modules\User\Models\User;
use Core\Enums\Messages\MessageTargetEnum;
use Core\Enums\Messages\MessageTypeEnum;
use Core\Services\AlertMessages\CustomHandlers\CustomMessageHandler;

class UserEmailVerifiedAlertMessageHandler implements CustomMessageHandler
{
    public function handle(User $user): ?AlertMessageEntity
    {
        if (!$user->isOwner()) {
            return null;
        }

        return $user->isEmailVerified()
            ? null
            : new AlertMessageEntity(
                __('messages.user.email-is-not-verified'),
                MessageTypeEnum::WARNING,
                MessageTargetEnum::EMAIL_NOT_VERIFIED
            );
    }
}
