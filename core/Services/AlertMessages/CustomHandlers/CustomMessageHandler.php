<?php

namespace Core\Services\AlertMessages\CustomHandlers;

use App\Entities\Messages\AlertMessageEntity;
use App\Modules\User\Models\User;

interface CustomMessageHandler
{
    public function handle(User $user): ?AlertMessageEntity;
}
