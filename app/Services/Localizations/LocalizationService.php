<?php

namespace App\Services\Localizations;

use App\Modules\Localization\Models\Language;
use Illuminate\Database\Eloquent\Collection;

class LocalizationService
{
    /**
     * @var null|Language
     */
    private static $language;

    /**
     * @var null|Collection|Language[]
     */
    private static $languages;

    public function getDefaultSlug(): string
    {
        return $this->getDefault()->slug;
    }

    public function getDefault(): Language
    {
        if (is_null(self::$language)) {
            self::$language = Language::default()->first();
        }

        return self::$language;
    }

    public function hasLang(string $lang): bool
    {
        return $this->getAllLanguages()->has($lang);
    }

    public function getAllLanguages(): Collection
    {
        if (is_null(self::$languages)) {
            self::$languages = Language::query()
                ->cacheFor(config('queries.localization.languages.cache'))
                ->get()
                ->keyBy('slug');
        }

        return self::$languages;
    }
}
