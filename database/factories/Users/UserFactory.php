<?php

namespace Database\Factories\Users;

use App\Modules\User\Models\User;
use App\ValueObjects\Email;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @method User|User[]|Collection create(array $attributes = [])
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->firstName,
            'email' => new Email($this->faker->unique()->safeEmail),
            'email_verified_at' => null,
            'email_verification_code' => null,
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'lang' => default_lang()->slug,
        ];
    }
}
