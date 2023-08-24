<?php

namespace App\Modules\Utils\Phones\Services;

use App\Modules\Utils\Phones\Events\RequestVerifyEvent;
use App\Modules\Utils\Phones\Exceptions\PhoneVerificationException;
use App\Modules\Utils\Phones\Models\Phone;
use App\Modules\Utils\Tokenizer\Tokenizer;
use App\Modules\Utils\Tokenizer\Traits\CodeGenerator;
use Carbon\CarbonImmutable;

final class VerificationService extends PhoneService
{
    use CodeGenerator;

    public function requestVerify(Phone $model): string
    {
        if($model->isVerify()){
           throw new PhoneVerificationException(
               __('exceptions.phone.verify.phone_already_verified')
           );
        }

        $model->code = $this->verifyCodeForPhone();
        $model->code_expired_at = CarbonImmutable::now()->addSeconds(
            config('sms.verify.sms_token_expired')
        );
        $model->save();


        event(new RequestVerifyEvent($model));

        return Tokenizer::encryptToken([
            'model_id' => $model->id,
            'model_class' => $model::class,
            'field_check_code' => 'code',
            'code' => $model->code,
        ]);
    }

    public function verify(string $token, int $code): bool
    {
        $token = Tokenizer::decryptToken($token);

        /** @var $model Phone */
        $model = $token->modelClass::find($token->modelId);

        if(!$model){
            throw new PhoneVerificationException(
                __('exceptions.phone.not_found_phone')
            );
        }

        if(CarbonImmutable::now() > $model->code_expired_at){
            throw new PhoneVerificationException(
                __('exceptions.phone.verify.code_has_expired')
            );
        }

        if((int)$model->{$token->fieldCheckCode} !== $code){
            throw new PhoneVerificationException(
                __('exceptions.phone.verify.code_is_not_correct')
            );
        }

        $this->verifyPhone($model);

        return true;
    }
}
