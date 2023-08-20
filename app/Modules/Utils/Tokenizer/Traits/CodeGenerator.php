<?php

namespace App\Modules\Utils\Tokenizer\Traits;

trait CodeGenerator
{
    protected function verificationCodeForEmail(): string
    {
        return random_int(100000, 999999);
    }

    protected function verifyCodeForPhone(): int
    {
        $length = config('sms.verify.code_length');

        return random_int($this->getMin($length), $this->getMax($length));
    }

    private function getMin(int $length): int
    {
        $min = '1';
        for ($i = 0; $i < $length - 1; $i++){
            $min .= '0';
        }

        return (int)$min;
    }

    private function getMax(int $length): int
    {
        $max = '9';
        for ($i = 0; $i < $length - 1; $i++){
            $max .= '9';
        }

        return (int)$max;
    }
}
