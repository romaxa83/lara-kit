<?php

namespace App\Providers;

use App\Modules\Admin\Events\AdminCreatedEvent;
use App\Modules\Admin\Listeners\SendAdminCredentialsListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        AdminCreatedEvent::class => [
            SendAdminCredentialsListener::class
        ]
    ];

    public function listens(): array
    {
        return parent::listens()
            + config('events.default');
    }
}
