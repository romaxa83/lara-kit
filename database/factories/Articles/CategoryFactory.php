<?php

namespace Database\Factories\Articles;

use App\Modules\Articles\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        return [
            'sort' => 1,
        ];
    }
}

