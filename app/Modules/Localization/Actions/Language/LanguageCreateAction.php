<?php

namespace App\Modules\Localization\Actions\Language;

use App\Enums\CacheKeyEnum;
use App\Modules\Localization\Dto\LanguageDto;
use App\Modules\Localization\Exceptions\LocalizationException;
use App\Modules\Localization\Models\Language;
use App\Modules\Localization\Repositories\LanguageRepository;
use Illuminate\Support\Facades\Cache;

final class LanguageCreateAction
{
    public function __construct(protected LanguageRepository $repo)
    {}

    public function exec(LanguageDto $dto): Language
    {
        if($dto->default && $this->repo->existBy(['default' => true])){
            throw new LocalizationException(
                __('exceptions.localization.default_language_can_be_only_one')
            );
        }
        if(
            (!$dto->default || !$dto->active)
            && $this->repo->count() == 0
        ){
            throw new LocalizationException(
                __('exceptions.localization.first_language_must_be_active_and_default')
            );
        }

        $model = new Language();

        $model->name = $dto->name;
        $model->slug = $dto->slug;
        $model->locale = $dto->locale;
        $model->default = $dto->default;
        $model->active = $dto->active;
        $model->sort = $dto->sort;

        $model->save();

        Cache::tags(CacheKeyEnum::LANGUAGE)->flush();

        return $model;
    }


}
