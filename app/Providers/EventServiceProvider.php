<?php

namespace App\Providers;

use App\Modules\Admin\Events\AdminCreatedEvent;
use App\Modules\Admin\Listeners\SendAdminCredentialsListener;
use App\Modules\Utils\Phones\Events\RequestVerifyEvent;
use App\Modules\Utils\Phones\Listeners\SendSmsListeners;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        AdminCreatedEvent::class => [
            SendAdminCredentialsListener::class
        ],
        RequestVerifyEvent::class => [
            SendSmsListeners::class
        ]
    ];

    public function listens(): array
    {
        return parent::listens()
            + config('events.default');
    }
}
