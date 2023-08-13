<?php

namespace Core\Services\AlertMessages;

use App\Entities\Messages\AlertMessageEntity;
use App\Modules\User\Models\User;
use Core\Services\AlertMessages\CustomHandlers\CustomMessageHandler;
use Generator;
use Illuminate\Support\Collection;

class CustomAlertMessageService
{
    /**
     * @param User $user
     * @return Collection<AlertMessageEntity>
     */
    public function getForUser(User $user): Collection
    {
        $messages = collect();

        foreach ($this->getNotificationHandler() as $handler) {
            if ($message = $handler->handle($user)) {
                $messages->push($message);
            }
        }

        return $messages;
    }

    /**
     * @return Generator|CustomMessageHandler[]
     */
    protected function getNotificationHandler(): Generator|array
    {
        foreach (config('notifications.custom.handlers') as $class) {
            yield app($class);
        }
    }
}
