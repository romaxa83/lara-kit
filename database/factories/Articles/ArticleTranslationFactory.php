<?php

namespace Database\Factories\Articles;

use App\Modules\Articles\Models\Article;
use App\Modules\Articles\Models\ArticleTranslation;
use App\Modules\Localization\Models\Language;
use Database\Factories\BaseTranslationFactory;
use Illuminate\Database\Eloquent\Collection;

/**
 * @method Collection|ArticleTranslation[]|ArticleTranslation create(array $attributes = [])
 */
class ArticleTranslationFactory extends BaseTranslationFactory
{
    protected $model = ArticleTranslation::class;

    public function definition(): array
    {
        return [
            'row_id' => Article::factory(),
            'lang' => Language::factory(),
            'title' => $this->faker->sentence,
            'text' => $this->faker->sentence,
            'description' => $this->faker->sentence,
        ];
    }
}


