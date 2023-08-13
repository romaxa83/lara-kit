<?php

namespace App\Models;

interface ListPermission
{
    public function canBeDeleted(): bool;

    public function canBeUpdated(): bool;

    public function getDeletePermissionKey(): string;

    public function getUpdatePermissionKey(): string;
}
