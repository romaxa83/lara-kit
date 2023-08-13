<?php

namespace Database\Factories\Articles;

use App\Modules\Articles\Models\Category;
use App\Modules\Articles\Models\CategoryTranslation;
use App\Modules\Localization\Models\Language;
use Database\Factories\BaseTranslationFactory;
use Illuminate\Database\Eloquent\Collection;

/**
 * @method Collection|CategoryTranslation[]|CategoryTranslation create(array $attributes = [])
 */
class CategoryTranslationFactory extends BaseTranslationFactory
{
    protected $model = CategoryTranslation::class;

    public function definition(): array
    {
        return [
            'row_id' => Category::factory(),
            'lang' => Language::factory(),
            'title' => $this->faker->sentence,
            'description' => $this->faker->sentence,
        ];
    }
}

