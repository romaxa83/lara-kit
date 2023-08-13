<?php

namespace App\Modules\Localization\Repositories;

use App\Enums\CacheKeyEnum;
use App\Modules\Localization\Collections\LanguageEloquentCollection;
use App\Modules\Localization\Models\Language;
use Core\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

final class LanguageRepository extends BaseRepository
{
    protected function modelClass(): string
    {
        return Language::class;
    }

    public function getLanguagesAsArray(string $key = 'slug', string $value = 'name'): array
    {
        return Cache::tags(CacheKeyEnum::LANGUAGE)
            ->rememberForever(CacheKeyEnum::LANGUAGE . '_' . $key . '_' . $value,
                fn(): array =>
                    DB::table(Language::TABLE)
                        ->select([$key, $value])
                        ->where('active', true)
                        ->get()
                        ->pluck($value, $key)
                        ->toArray()
                );
    }

    public function getLanguages(
        array $select = ['*'],
        array $filters = []
    ): LanguageEloquentCollection
    {
        return Cache::tags(CacheKeyEnum::LANGUAGE)
            ->rememberForever(CacheKeyEnum::LANGUAGE .'_'. implode('_', $select).'_'.implode('_', $filters),
                fn() => Language::query()
                    ->select($select)
                    ->filter($filters)
                    ->when(!array_key_exists('sort', $filters),
                        fn(Builder $b) => $b->orderBy('sort')
                    )
                    ->get()
            );
    }

    public function getDefault(): ?Language
    {
        return Cache::tags(CacheKeyEnum::LANGUAGE)
            ->rememberForever(CacheKeyEnum::DEFAULT_LANGUAGE,
                fn(): ?Language =>
                    Language::query()
                        ->where('default', true)
                        ->first()
            );
    }

    public function existActiveWithoutId($withoutId): bool
    {
        return Language::query()
            ->active()
            ->whereNot('id', $withoutId)
            ->exists()
            ;
    }
}
