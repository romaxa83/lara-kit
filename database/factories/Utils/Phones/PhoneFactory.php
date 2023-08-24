<?php

namespace Database\Factories\Utils\Phones;

use App\Modules\User\Models\User;
use App\Modules\Utils\Phones\Models\Phone as PhoneModel;

use App\Modules\Utils\Phones\ValueObject\Phone;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @method PhoneModel|PhoneModel[]|Collection create(array $attributes = [])
 */
class PhoneFactory extends Factory
{
    protected $model = PhoneModel::class;

    public function definition(): array
    {
        $user = User::factory()->create();
        return [
            'model_type' => $user::class,
            'model_id' => $user->id,
            'phone' => new Phone($this->faker->phoneNumber),
            'phone_verified_at' => null,
            'code' => null,
            'code_expired_at' => null,
            'default' => true,
        ];
    }
}

