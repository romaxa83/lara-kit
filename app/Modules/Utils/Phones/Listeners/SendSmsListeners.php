<?php

namespace App\Modules\Utils\Phones\Listeners;

use App\Enums\LogKey;
use App\Modules\Utils\Phones\Contracts\SmsSendable;
use App\Modules\Utils\Phones\Services\SmsSender\SmsSender;
use Exception;

class SendSmsListeners
{
    public function __construct(protected SmsSender $sender)
    {}

    /**
     * @throws Exception
     */
    public function handle(SmsSendable $event): void
    {
        dd('d');
        try {
            $this->sender->send($event->getPhone(), $event->getMsg());

            logger_info(LogKey::SEND_SMS."Send to [{$event->getPhone()->getValue()}], with msg: \"{$event->getMsg()}\" SUCCESS");

        } catch (\Throwable $e) {

            logger_info( LogKey::SEND_SMS."Send to [{$event->getPhone()->getValue()}], with msg: \"{$event->getMsg()}\" FAILED " . __CLASS__, [
                'message' => $e->getMessage()
            ]);
        }
    }
}


