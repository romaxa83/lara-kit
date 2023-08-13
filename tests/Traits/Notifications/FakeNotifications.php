<?php

namespace Tests\Traits\Notifications;

use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;

trait FakeNotifications
{
    protected function assertNotificationSentTo(string $email, string $notification, string $channel = 'mail'): void
    {
        Notification::assertSentTo(
            (new AnonymousNotifiable())->route($channel, $email),
            $notification
        );
    }
}
