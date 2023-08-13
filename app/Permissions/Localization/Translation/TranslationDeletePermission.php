<?php

namespace App\Permissions\Localization\Translation;

use Core\Permissions\BasePermission;

class TranslationDeletePermission extends BasePermission
{
    public const KEY = 'translation.delete';

    public function getName(): string
    {
        return __('permissions.translation.grants.delete');
    }

    public function getPosition(): int
    {
        return 3;
    }
}
