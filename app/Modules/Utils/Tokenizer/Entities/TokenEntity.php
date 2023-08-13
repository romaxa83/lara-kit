<?php

namespace App\Modules\Utils\Tokenizer\Entities;

class TokenEntity
{
    public int $modelId;
    public string $modelClass;
    public int $timeAt;
    public string $fieldCheckCode;
    public int $code;

    public function __construct(array $payload)
    {
        $this->modelId = $payload['model_id'];
        $this->modelClass = $payload['model_class'];
        $this->timeAt = $payload['time_at'];
        $this->fieldCheckCode = $payload['field_check_code'];
        $this->code = (int)$payload['code'];
    }
}
