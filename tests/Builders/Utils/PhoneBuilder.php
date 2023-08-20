<?php

namespace Tests\Builders\Utils;

use App\Modules\Utils\Phones\Models\Phone;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Tests\Builders\BaseBuilder;

class PhoneBuilder extends BaseBuilder
{
    function modelClass(): string
    {
        return Phone::class;
    }

    public function code(int $value, CarbonImmutable $expiredAt): self
    {
        $this->data['code'] = $value;
        $this->data['code_expired_at'] = $expiredAt;
        return $this;
    }

    public function model(Model $model): self
    {
//        dd($model);
        $this->data['model_id'] = $model->id;
        $this->data['model_type'] = $model::MORPH_NAME;

        return $this;
    }
}

