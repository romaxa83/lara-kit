<?php

namespace App\Modules\Utils\Phones\Contracts;

use App\Modules\Utils\Phones\Models\Phone;

interface Phoneable
{
    public function getMorphType(): string;

    public function getId(): string;

    public function getPhoneAttribute(): null|Phone;
}
