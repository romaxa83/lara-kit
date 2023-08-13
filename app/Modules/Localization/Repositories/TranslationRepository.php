<?php

namespace App\Modules\Localization\Repositories;

use App\Enums\CacheKeyEnum;
use App\Modules\Localization\Models\Translation;
use Core\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

final class TranslationRepository extends BaseRepository
{
    protected function modelClass(): string
    {
        return Translation::class;
    }

    public function getTranslationsAsArray(
        array $select = ['*'],
        array $filters = []
    ): array
    {
        return Cache::tags(CacheKeyEnum::TRANSLATIONS)
            ->remember(
                CacheKeyEnum::TRANSLATIONS .'_'. hash_data(array_merge($select, $filters)),
                CacheKeyEnum::TRANSLATIONS_TIME ,
                fn() => Translation::query()
                    ->select($select)
                    ->filter($filters)
                    ->when(!array_key_exists('sort', $filters),
                        fn(Builder $b) => $b->orderBy('id')
                    )
                    ->getQuery()
                    ->get()
                    ->toArray()
            );
    }

    public function getTranslationsAsPaginator(
        array $select = ['*'],
        array $filters = []
    ): LengthAwarePaginator
    {
        return Cache::tags(CacheKeyEnum::TRANSLATIONS)
            ->remember(
                CacheKeyEnum::TRANSLATIONS .'_'. hash_data(array_merge($select, $filters)),
                CacheKeyEnum::TRANSLATIONS_TIME ,
                fn() => Translation::query()
                    ->select($select)
                    ->filter($filters)
                    ->when(!array_key_exists('sort', $filters),
                        fn(Builder $b) => $b->orderBy('id')
                    )
                    ->getQuery()
                    ->paginate(
                        perPage: $this->getPerPage($filters),
                        page: $this->getPage($filters)
                    )
            );
    }
}
