<?php

namespace App\Modules\Localization\Actions\Language;

use App\Enums\CacheKeyEnum;
use App\Modules\Localization\Exceptions\LocalizationException;
use App\Modules\Localization\Models\Language;
use App\Modules\Localization\Repositories\LanguageRepository;
use Illuminate\Support\Facades\Cache;

final class LanguageToggleActiveAction
{
    public function __construct(protected LanguageRepository $repo)
    {}

    public function exec(Language $model): Language
    {
        if($model->isActive() && $model->isDefault()){
            throw new LocalizationException(
                __('exceptions.localization.can\'t_disable_default_language')
            );
        }

        $model->active = !$model->active;

        $model->save();

        Cache::tags(CacheKeyEnum::LANGUAGE)->flush();

        return $model;
    }
}

