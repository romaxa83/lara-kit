<?php

namespace App\Modules\Utils\Phones\Events;

use App\Modules\Utils\Phones\Contracts\SmsSendable;
use App\Modules\Utils\Phones\Models\Phone;
use App\Modules\Utils\Phones\ValueObject\Phone as PhoneObj;

class RequestVerifyEvent implements SmsSendable
{
    public function __construct(
        protected Phone $model,
    )
    {}

    public function getModel(): Phone
    {
        return $this->model;
    }

    public function getPhone(): PhoneObj
    {
        return $this->model->phone;
    }

    public function getMsg(): string
    {
        return __('messages.phone.sms.verify_code', ['code' => $this->model->code]);
    }
}

