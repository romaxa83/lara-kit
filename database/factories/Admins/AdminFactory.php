<?php

namespace Database\Factories\Admins;

use App\Modules\Admin\Models\Admin;
use App\ValueObjects\Email;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @method static Admin|Admin[]|Collection create(array $attributes = [])
 */
class AdminFactory extends Factory
{

    protected $model = Admin::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->lastName . ' '. $this->faker->firstName,
            'email' => new Email($this->faker->unique()->safeEmail),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'lang' => default_lang()->slug,
        ];
    }
}
