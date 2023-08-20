<?php

namespace App\Modules\Utils\Phones\Contracts;


use App\Modules\Utils\Phones\ValueObject\Phone;

interface SmsSendable
{
    public function getPhone(): Phone;

    public function getMsg(): string;
}
