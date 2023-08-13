<?php

namespace App\Permissions\Localization\Translation;

use Core\Permissions\BasePermissionGroup;

class TranslationPermissionGroup extends BasePermissionGroup
{
    public const KEY = 'translation';

    public function getName(): string
    {
        return __('permissions.translation.group');
    }

    public function getPosition(): int
    {
        return 10;
    }
}
