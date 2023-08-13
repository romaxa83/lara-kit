<?php

namespace Database\Factories\Localization;

use App\Modules\Localization\Models\Translation;
use Illuminate\Database\Eloquent\Factories\Factory;

class TranslationFactory extends Factory
{
    protected $model = Translation::class;

    public function definition(): array
    {
        return [
            'place' => $this->faker->unique()->word,
            'key' => $this->faker->unique()->word,
            'text' => $this->faker->sentence,
            'lang' => default_lang()->slug,
        ];
    }
}
