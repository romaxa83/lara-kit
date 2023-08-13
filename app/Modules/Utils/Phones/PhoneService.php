<?php

namespace App\Modules\Utils\Phones;

use App\Modules\Utils\Phones\Contracts\Phoneable;
use App\Modules\Utils\Phones\Models\Phone as PhoneModel;
use App\Modules\Utils\Phones\ValueObject\Phone;
use Carbon\CarbonImmutable;

class PhoneService
{
    public function createOrUpdate(
        Phoneable $phoneable,
        Phone $phone,
        bool $verifyPhone = false
    ): PhoneModel
    {
        if($phoneable->getPhoneAttribute()){
            return $this->update($phoneable, $phone, $verifyPhone);
        }

        return $this->create($phoneable, $phone, $verifyPhone);
    }

    public function create(
        Phoneable $phoneable,
        Phone $phone,
        bool $verifyPhone = false
    ): PhoneModel
    {
        $model = new PhoneModel();
        $model->model_type = $phoneable->getMorphType();
        $model->model_id = $phoneable->getId();
        $model->phone = $phone;
        $model->default = true;

        if($verifyPhone){
            $model = $this->verifyPhone($model, false);
        }

        $model->save();

        return $model;
    }

    public function update(
        Phoneable $phoneable,
        Phone $phone,
        bool $verifyPhone = false
    ): PhoneModel
    {
        $model = $phoneable->getPhoneAttribute();

        if(!$model->phone->compare($phone)){
            $model->phone = $phone;
            $model->phone_verified_at = null;

            if($verifyPhone){
                $model = $this->verifyPhone($model, false);
            }

            $model->save();
        }

        return $model;
    }

    public function verifyPhone(PhoneModel $model, bool $save = true): PhoneModel
    {
        $model->phone_verified_at = CarbonImmutable::now();
        $model->code = null;
        $model->code_expired_at = null;

        if($save){
            $model->save();
        }

        return $model;
    }
}
