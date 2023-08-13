<?php

namespace App\Services\Localizations;

use App\Dto\Locales\LocaleDto;
use App\Models\Localization\Locale;
use Illuminate\Database\Eloquent\Collection;

class LocaleService
{
    /**
     * @var null|Collection|Locale[]
     */
    private static null|Collection|array $locales;

    /**
     * @param LocaleDto $dto
     * @return Locale
     */
    public function create(LocaleDto $dto): Locale
    {
        $locale = new Locale();
        $this->fill($locale, $dto);
        $locale->save();

        return $locale;
    }

    protected function fill(Locale $locale, LocaleDto $dto): Locale
    {
        $locale->slug = $dto->getSlug();
        $locale->name = $dto->getName();
        $locale->native = $dto->getNative();
        $locale->sort = $dto->getSort();

        return $locale;
    }

    public function update(Locale $locale, LocaleDto $dto): Locale
    {
        $dto->setSlug($locale->slug);
        $this->fill($locale, $dto);
        $locale->save();

        return $locale;
    }

    public function delete(Locale $locale): bool
    {
        if ($locale->canBeDeleted()) {
            return $locale->delete();
        }

        return false;
    }

    public function sort(array $ids): Collection
    {
        collect($ids)
            ->each(static fn($id, $key) => [
                Locale::query()
                    ->where('id', $id)
                    ->update(['sort' => $key + 1])
            ]);

        return Locale::query()
            ->orderBy('sort')
            ->get();
    }

    public function getAllLocales(): Collection
    {
        if (is_null(self::$locales)) {
            self::$locales = Locale::query()
                ->cacheFor(config('queries.localization.locales.cache'))
                ->get()
                ->keyBy('slug');
        }

        return self::$locales;
    }
}
