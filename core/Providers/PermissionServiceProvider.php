<?php

namespace Core\Providers;

use Core\Services\Permissions\PermissionFilterService;
use Illuminate\Support\ServiceProvider;

class PermissionServiceProvider extends ServiceProvider
{
    public array $singletons = [
        PermissionFilterService::class => PermissionFilterService::class,
    ];
}
