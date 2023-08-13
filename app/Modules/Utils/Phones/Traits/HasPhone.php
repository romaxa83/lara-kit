<?php

namespace App\Modules\Utils\Phones\Traits;

use App\Modules\Utils\Phones\Models\Phone;
use App\Modules\Utils\Phones\ValueObject\Phone as PhoneObj;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @see HasPhone::getPhoneAttribute()
 * @property-read null|Phone phone
 *
 * @see HasPhone::phones()
 * @property-read Collection|Phone[] phones
 */
trait HasPhone
{
    public function getMorphType(): string
    {
        return self::MORPH_NAME;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function phones(): MorphMany|Phone
    {
        return $this->morphMany(Phone::class, 'model');
    }

    public function getPhoneAttribute(): null|Phone
    {
        return $this->phones()->default()->first();
    }

    public function getPhone(): null|PhoneObj
    {
        return $this->phones()?->default()->first()->phone;
    }

    public function isPhoneVerified(): bool
    {
        return $this?->phone?->phone_verified_at !== null;
    }
}

