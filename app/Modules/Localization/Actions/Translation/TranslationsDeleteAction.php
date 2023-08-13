<?php

namespace App\Modules\Localization\Actions\Translation;

use App\Enums\CacheKeyEnum;
use App\Modules\Localization\Models\Translation;
use Illuminate\Support\Facades\Cache;

final class TranslationsDeleteAction
{
    public function __construct()
    {}

    public function exec(array $data): bool
    {
        Cache::tags(CacheKeyEnum::TRANSLATIONS)->flush();
//dd($data);
        make_transaction(function() use ($data){
            foreach ($data as $item){
//                $this->remove($item);
                if(!$this->remove($item)){
                    throw new \Exception(__('messages.localization.translation.actions.delete.fail'));
                }
            }
        });

        return true;
    }

    private function remove($data): bool
    {
        return Translation::query()
            ->where('place', $data['place'])
            ->where('key', $data['key'])
            ->where('lang', $data['lang'])
            ->delete();
    }
}


