<?php

namespace App\Modules\Localization\Actions\Translation;

use App\Enums\CacheKeyEnum;
use App\Modules\Localization\Models\Translation;
use Illuminate\Support\Facades\Cache;

final class TranslationsCreateOrUpdateAction
{
    public function __construct()
    {}

    public function exec(array $data): bool
    {
        Cache::tags(CacheKeyEnum::TRANSLATIONS)->flush();

        return (bool)Translation::query()->upsert($data, ['place', 'key', 'lang'], ['text']);
    }
}

