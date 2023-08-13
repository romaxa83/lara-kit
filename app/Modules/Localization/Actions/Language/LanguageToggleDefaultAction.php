<?php

namespace App\Modules\Localization\Actions\Language;

use App\Enums\CacheKeyEnum;
use App\Modules\Localization\Exceptions\LocalizationException;
use App\Modules\Localization\Models\Language;
use App\Modules\Localization\Repositories\LanguageRepository;
use Illuminate\Support\Facades\Cache;

final class LanguageToggleDefaultAction
{
    public function __construct(protected LanguageRepository $repo)
    {}

    public function exec(Language $model): Language
    {
        if($model->isDefault()){
            if($anotherModel = $this->repo->getBy('active', true, withoutId: $model->id)){
                $anotherModel->update(['default' => true]);
            } else {
                throw new LocalizationException(
                    __('exceptions.localization.can\'t_toggle_not_another_active_lang')
                );
            }
        } else {
            $defaultModel = $this->repo->getBy('default', true);
            $defaultModel->update(['default' => false]);
        }

        $model->default = !$model->default;

        $model->save();

        Cache::tags(CacheKeyEnum::LANGUAGE)->flush();

        return $model;
    }
}
