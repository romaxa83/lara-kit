<?php

namespace Database\Factories\Permissions;

use App\Modules\Admin\Models\Admin;
use App\Modules\Permissions\Models\Role;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @method Role|Role[]|Collection create(array $attributes = [])
 */
class RoleFactory extends Factory
{
    protected $model = Role::class;

    public function definition(): array
    {
        return [
            'guard_name' => Admin::GUARD,
            'name' => $this->faker->jobTitle,
        ];
    }

    public function asDefault(): self
    {
        return $this->state(
            [
                'for_owner' => true,
            ]
        );
    }

    public function admin(): self
    {
        return $this->state(
            [
                'guard_name' => Admin::GUARD
            ]
        );
    }
}
