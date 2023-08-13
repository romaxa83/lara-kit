<?php

namespace Database\Factories\Permissions;

use App\Modules\Admin\Models\Admin;
use App\Modules\Permissions\Models\Permission;
use App\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @method static Permission|Permission[]|Collection create(array $attributes = [])
 */
class PermissionFactory extends Factory
{
    protected $model = Permission::class;

    public function definition(): array
    {
        return [
            'guard_name' => User::GUARD,
            'name' => $this->faker->sentence,
        ];
    }

    public function admin()
    {
        return $this->state(
            function (array $attributes) {
                return [
                    'guard_name' => Admin::GUARD
                ];
            }
        );
    }
}
