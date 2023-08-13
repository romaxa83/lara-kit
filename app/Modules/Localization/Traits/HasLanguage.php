<?php

namespace App\Modules\Localization\Traits;

use App\Modules\Localization\Models\Language;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string lang
 *
 * @property-read Language language
 */

trait HasLanguage
{
    public function getLangSlug(): ?string
    {
        return $this->lang;
    }

    public function setLang(string $lang): void
    {
        $this->lang = $lang;
        $this->save();
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'lang', 'slug');
    }
}


