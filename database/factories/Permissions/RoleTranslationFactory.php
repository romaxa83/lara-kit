<?php

namespace Database\Factories\Permissions;

use App\Modules\Permissions\Models\RoleTranslation;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @method RoleTranslation|Collection create(array $attributes = [])
 */
class RoleTranslationFactory extends Factory
{
    protected $model = RoleTranslation::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->unique()->jobTitle,
        ];
    }
}
