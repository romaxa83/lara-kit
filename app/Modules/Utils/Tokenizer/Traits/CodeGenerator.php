<?php

namespace App\Modules\Utils\Tokenizer\Traits;

trait CodeGenerator
{
    protected function verificationCodeForEmail(): string
    {
        return random_int(100000, 999999);
    }
}
