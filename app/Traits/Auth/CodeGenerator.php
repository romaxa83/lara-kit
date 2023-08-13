<?php

namespace App\Traits\Auth;

use Exception;

trait CodeGenerator
{
    /** @throws Exception */
    protected function generateVerificationCode(): string
    {
        return random_int(100000, 999999);
    }
}
