<?php

namespace Tests\Traits\Assert;

use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;

trait AssertNotification
{
    protected function assertNotificationSentTo(string $email, string $notification, string $channel = 'mail'): void
    {
        Notification::assertSentTo(
            (new AnonymousNotifiable())->route($channel, $email),
            $notification
        );
    }

    protected function assertNotificationNotSentTo(string $email, string $notification, string $channel = 'mail'): void
    {
        Notification::assertNotSentTo(
            (new AnonymousNotifiable())->route($channel, $email),
            $notification
        );
    }
}


