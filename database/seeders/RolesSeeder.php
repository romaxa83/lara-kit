<?php

namespace Database\Seeders;

use App\Modules\Permissions\Enums\BaseRole;
use App\Modules\Permissions\Services\PermissionService;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    public function __construct(protected PermissionService $service)
    {}

    public function run(): void
    {
        foreach (BaseRole::list() as $role){
            $this->service->createBaseRole($role);
        }
    }
}
