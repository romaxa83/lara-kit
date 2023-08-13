<?php

namespace App\Traits;

use App\Modules\Localization\Models\Language;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * @property-read object|mixed|null $row
 * @property-read Language $lang
 */
trait ModelTranslation
{
    public function row(): BelongsTo|Language
    {
        return $this->belongsTo(
            $this->relatedModelName(),
            'row_id',
            'id'
        )
            ->withDefault();
    }

    public function relatedModelName(): string
    {
        return Str::replaceLast('Translation', '', static::class);
    }

    public function lang(): BelongsTo|Language
    {
        return $this->belongsTo(Language::class, 'slug', 'language')->withDefault();
    }
}
