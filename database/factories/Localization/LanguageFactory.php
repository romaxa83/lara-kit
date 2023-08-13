<?php

namespace Database\Factories\Localization;

use App\Modules\Localization\Models\Language;
use Illuminate\Database\Eloquent\Factories\Factory;

class LanguageFactory extends Factory
{
    protected $model = Language::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->country,
            'slug' => $this->faker->unique()->languageCode,
            'locale' => $this->faker->unique()->locale,
            'default' => false,
            'active' => true,
            'sort' => 1,
        ];
    }
}
