<?php

namespace Database\Seeders;

use App\Modules\Localization\Actions\Language\LanguageCreateAction;
use App\Modules\Localization\Dto\LanguageDto;
use App\Modules\Localization\Models\Language;
use Illuminate\Database\Seeder;

class LanguageDefaultSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->data() as $item){
            if(!Language::query()->where('slug', $item['slug'])->exists()){
                /** @var $handler LanguageCreateAction */
                $handler = resolve(LanguageCreateAction::class);
                $handler->exec(LanguageDto::byArgs($item));
            }
        }
    }

    protected function data(): array
    {
        return [
            [
                'slug' => 'en',
                'name' => 'English',
                'locale' => 'en_GB',
                'default' => true,
                'sort' => 1,
            ],
            [
                'slug' => 'ru',
                'locale' => 'ru_RU',
                'name' => 'Русский',
                'default' => false,
                'sort' => 2,
            ],
            [
                'slug' => 'uk',
                'locale' => 'uk_UA',
                'name' => 'Українська',
                'default' => false,
                'active' => false,
                'sort' => 3,
            ],
        ];
    }
}
