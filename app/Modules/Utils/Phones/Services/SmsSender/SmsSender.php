<?php

namespace App\Modules\Utils\Phones\Services\SmsSender;

use App\Modules\Utils\Phones\ValueObject\Phone;

interface SmsSender
{
    public function send(Phone $phone, string  $text): void;
}
