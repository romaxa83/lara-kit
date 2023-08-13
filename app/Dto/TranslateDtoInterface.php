<?php

namespace App\Dto;

interface TranslateDtoInterface
{
    public function fillTranslates(array $translates): TranslateDtoInterface;
}
