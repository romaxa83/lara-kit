<?php

namespace App\Modules\Utils\Phones\Services;

use App\Modules\Utils\Phones\Contracts\Phoneable;
use App\Modules\Utils\Phones\Dto\PhoneDto;
use App\Modules\Utils\Phones\Dto\PhonesDto;
use App\Modules\Utils\Phones\Exceptions\PhoneServiceException;
use App\Modules\Utils\Phones\Models\Phone as PhoneModel;
use Carbon\CarbonImmutable;

class PhoneService
{
    public function creates(
        Phoneable $phoneable,
        PhonesDto $dto
    )
    {
        foreach ($dto->phones as $phoneDto){
            $phoneDto->verify = $dto->verify;
            $this->create($phoneable, $phoneDto);
        }
    }

    public function createOrUpdates(
        Phoneable $phoneable,
        PhonesDto $dto
    )
    {
        foreach ($dto->phones as $phoneDto) {
            $phoneDto->verify = $dto->verify;
            if($model = $phoneable->phones()->where('phone', $phoneDto->phone)->first()){
                $this->update($model, $phoneDto);
            } else {
                $this->create($phoneable, $phoneDto);
            }
        }
    }

    public function createOrUpdate(
        Phoneable $phoneable,
        PhoneDto $dto
    ): PhoneModel
    {
        if($phoneable->getPhoneAttribute()){
            return $this->updateOnlyPhone($phoneable, $dto);
        }

        return $this->create($phoneable, $dto);
    }

    public function create(
        Phoneable $phoneable,
        PhoneDto $dto
    ): PhoneModel
    {
        if($phoneable->phones()->where('phone', $dto->phone)->exists()){
            throw new PhoneServiceException(__("exceptions.phone.duplicate_phone"));
        }

        $model = new PhoneModel();
        $model->model_type = $phoneable->getMorphType();
        $model->model_id = $phoneable->getId();
        $model->phone = $dto->phone;
        $model->default = $dto->default;
        $model->desc = $dto->desc;

        if($m = $phoneable->phones->last()){
            $model->sort = $m->sort + 1;
        }

        if($dto->verify){
            $model = $this->verifyPhone($model, false);
        }

        $model->save();

        return $model;
    }

    public function update(
        PhoneModel $model,
        PhoneDto $dto,
    ): PhoneModel
    {
        $model->desc = $dto->desc;
        $model->default = $dto->default;
        $model->phone_verified_at = null;

        if($dto->verify){
            $model = $this->verifyPhone($model, false);
        }

        $model->save();

        return $model;
    }

    public function updateOnlyPhone(
        Phoneable $phoneable,
        PhoneDto $dto,
    ): PhoneModel
    {
        $model = $phoneable->phone;

        if(!$model->phone->compare($dto->phone)){
            $model->phone = $dto->phone;
            $model->phone_verified_at = null;

            if($dto->verify){
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
