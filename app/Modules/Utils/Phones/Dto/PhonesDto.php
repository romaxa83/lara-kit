<?php

namespace App\Modules\Utils\Phones\Dto;

class PhonesDto
{
    /** @var array<PhoneDto> */
    public array $phones = [];
    public bool $verify = false;

    public static function byArgs(array $args): self
    {
        $dto = new self();

        foreach ($args as $item){
            $dto->phones[] = PhoneDto::byArgs($item);
        }

        return $dto;
    }
}
