<?php

namespace App\Permissions\Localization\Translation;

use Core\Permissions\BasePermission;

class TranslationListPermission extends BasePermission
{
    public const KEY = 'translation.list';

    public function getName(): string
    {
        return __('permissions.translation.grants.list');
    }

    public function getPosition(): int
    {
        return 1;
    }
}
