<?php

namespace Database\Factories\Permissions;

use App\Modules\Permissions\Models\PermissionTranslation;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @method PermissionTranslation|Collection create(array $attributes = [])
 */
class PermissionTranslationFactory extends Factory
{
    protected $model = PermissionTranslation::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->unique()->jobTitle,
        ];
    }
}
