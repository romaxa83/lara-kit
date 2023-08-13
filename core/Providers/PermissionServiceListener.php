<?php

namespace Core\Providers;

use Spatie\Permission\PermissionRegistrar;

class PermissionServiceListener
{
    public function handle($event): void
    {
        $event->sandbox[PermissionRegistrar::class]->clearClassPermissions();
    }

}
