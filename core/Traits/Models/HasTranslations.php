<?php

namespace Core\Traits\Models;

use Core\Models\BaseTranslation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Lang;

/**
 * @property Collection|BaseTranslation[] $translations
 * @property BaseTranslation $translation
 *
 * @see HasTranslations::scopeAddName()
 * @method Builder|static addName(string $field = 'title', ?string $as = null)
 *
 * @see HasTranslations::scopeJoinTranslation()
 * @method Builder|static joinTranslation(string $lang = 'en')
 */
trait HasTranslations
{
    public static function tableName(): string
    {
        $currentClass = static::class;
        return (new $currentClass())->getTable();
    }

    public function translations(): HasMany
    {
        return $this->hasMany(self::translationModelName(), 'row_id', 'id');
    }

    public static function translationModelName(): string
    {
        return static::class . 'Translation';
    }

    public function translation(): HasOne
    {
        return $this->hasOne(self::translationModelName(), 'row_id', 'id')
            ->where('lang', Lang::getLocale());
    }

    public function dataForCurrentLanguage($default = null)
    {
        $translations = $this->translations;
        foreach ($translations as $translation) {
            if ($translation->language === config('app.locale')) {
                return $translation;
            }
        }
        return $default;
    }

    public function dataFor($lang, $default = null)
    {
        $translations = $this->translations;
        foreach ($translations as $translation) {
            if ($translation->language === $lang) {
                return $translation;
            }
        }
        return $default;
    }

    public function scopeJoinTranslation(Builder $b, ?string $lang = null): void
    {
        if (is_null($lang)) {
            $lang = app()->getLocale();
        }

        $translationTable = static::getTranslationTableName();

        $b->join(
            $translationTable,
            $translationTable . '.row_id',
            '=',
            static::TABLE . '.id'
        )->where($translationTable . '.language', $lang);
    }

    public static function getTranslationTableName(): string
    {
        $translateModelName = static::translationModelName();
        return app($translateModelName)->getTable();
    }

    public function scopeAddName(Builder|self $b, string $field = 'title', ?string $as = null): void
    {
        $asField = $as ? $field . " as $as" : $field;

        $b->joinTranslation(app()->getLocale())
            ->addSelect(static::getTranslationTableName() . '.' . $asField);
    }
}
