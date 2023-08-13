<?php

namespace App\Traits;

/**
 * @method static static|Factory factory()
 */
trait MailNotificationChannelTrait
{
    /**
     * @return string[]
     */
    public function viaQueues()
    {
        return [
            'mail' => 'mail-notification'
        ];
    }
}
