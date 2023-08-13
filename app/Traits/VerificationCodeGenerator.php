<?php

namespace App\Traits;

use Exception;

trait VerificationCodeGenerator
{
    /**
     * @throws Exception
     */
    protected function generateVerificationCode(): string
    {
        return random_int(100000, 999999);
    }
}
