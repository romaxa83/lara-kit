<?php

namespace Database\Seeders;

use App\Modules\Permissions\Services\PermissionService;
use Illuminate\Database\Seeder;

class PermissionsSeeder extends Seeder
{
    public function __construct(protected PermissionService $service)
    {}

    public function run(): void
    {
        $this->service->seed();
    }
}
