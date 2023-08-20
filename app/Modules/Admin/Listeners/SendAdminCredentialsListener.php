<?php

namespace App\Modules\Admin\Listeners;

use App\Enums\LogKey;
use App\Modules\Admin\Events\AdminCreatedEvent;
use App\Modules\Admin\Notifications\AdminCredentialsNotification;
use Exception;
use Illuminate\Support\Facades\Notification;

class SendAdminCredentialsListener
{
    public function __construct()
    {}

    /**
     * @throws Exception
     */
    public function handle(AdminCreatedEvent $event): void
    {
        try {
            Notification::route('mail', $event->getModel()->email->getValue())
                ->notify(
                    (new AdminCredentialsNotification($event->getModel(), $event->getDto()->password))
                        ->locale($event->getModel()->lang)
                )
            ;

            logger_info(LogKey::SEND_EMAIL."SendAdminCredentials to [{$event->getModel()->email->getValue()}] SUCCESS");

        } catch (\Throwable $e) {
            logger_info( LogKey::SEND_EMAIL."SendAdminCredentials FAILED -" . __CLASS__, [
                'message' => $e->getMessage()
            ]);
        }
    }
}

