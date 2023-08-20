<?php

namespace App\Modules\Utils\Phones\Services\SmsSender;

use App\Modules\Utils\Phones\ValueObject\Phone;

class ArraySender implements SmsSender
{
    private $messages = [];

    public function send(Phone $phone, string  $text): void
    {
        $this->messages[] = [
            'to' => $phone->getValue(),
            'text' => $text
        ];
    }

    public function getMessages(): array
    {
        return $this->messages;
    }
}
