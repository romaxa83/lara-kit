<?php

namespace App\Permissions\Localization\Translation;

use Core\Permissions\BasePermission;

class TranslationUpdatePermission extends BasePermission
{
    public const KEY = 'translation.update';

    public function getName(): string
    {
        return __('permissions.translation.grants.update');
    }

    public function getPosition(): int
    {
        return 2;
    }
}
