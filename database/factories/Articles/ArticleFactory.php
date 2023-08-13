<?php

namespace Database\Factories\Articles;

use App\Modules\Articles\Enums\ArticleStatus;
use App\Modules\Articles\Models\Article;
use App\Modules\Articles\Models\Category;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArticleFactory extends Factory
{
    protected $model = Article::class;

    public function definition(): array
    {
        return [
            'status' => ArticleStatus::DRAFT,
            'category_id' => Category::factory(),
            'published_at' => CarbonImmutable::now(),
        ];
    }
}


