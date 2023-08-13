<?php

namespace App\Modules\Utils\Tokenizer\Traits;

use App\Modules\Utils\Tokenizer\Tokenizer;
use Carbon\CarbonImmutable;
use Core\Models\BaseAuthenticatable;

trait EmailCryptoToken
{
    protected function encrypt(BaseAuthenticatable $model): string
    {
        return Tokenizer::encryptToken([
            'model_id' => $model->id,
            'model_class' => $model::class,
            'time_at' => CarbonImmutable::now()->timestamp,
            'field_check_code' => 'email_verification_code',
            'code' => $model->email_verification_code,
        ]);
    }
}
