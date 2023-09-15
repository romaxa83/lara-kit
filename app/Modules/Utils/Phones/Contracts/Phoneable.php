<?php

namespace App\Modules\Utils\Phones\Contracts;

use App\Modules\Utils\Phones\Collections\PhoneEloquentCollection;
use App\Modules\Utils\Phones\Models\Phone;
use Illuminate\Database\Eloquent\Relations\MorphMany;

interface Phoneable
{
    public function getMorphType(): string;

    public function getId(): string;

    public function getPhoneAttribute(): null|Phone;

    public function phones(): MorphMany|PhoneEloquentCollection;
}
