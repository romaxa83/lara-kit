<?php

namespace App\Modules\Utils\Phones\Traits;

use App\Modules\Utils\Phones\Collections\PhoneEloquentCollection;
use App\Modules\Utils\Phones\Models\Phone;
use App\Modules\Utils\Phones\ValueObject\Phone as PhoneObj;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @see HasPhone::getPhoneAttribute()
 * @property-read null|Phone phone
 *
 * @see HasPhone::phones()
 * @property-read PhoneEloquentCollection|Phone[] phones
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

    public function phones(): MorphMany|PhoneEloquentCollection
    {
        return $this->morphMany(Phone::class, 'model')
            ->orderBy('sort');
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
        return $this?->phone?->isVerify();
    }
}
