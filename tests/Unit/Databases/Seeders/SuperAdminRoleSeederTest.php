<?php

declare(strict_types=1);

namespace Tests\Unit\Databases\Seeders;


use App\Modules\Admin\Models\Admin;
use App\Modules\Admin\Repositories\AdminRepository;
use App\Modules\Permissions\Enums\BaseRole;
use App\Modules\Permissions\Enums\Guard;
use App\Modules\Permissions\Models\Permission;
use Core\Services\Permissions\PermissionService;

use Database\Seeders\SuperAdminSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Builders\Permissions\RoleBuilder;
use Tests\TestCase;

class SuperAdminRoleSeederTest extends TestCase
{
    use DatabaseTransactions;

    protected PermissionService $permissionService;
    protected RoleBuilder $roleBuilder;
    protected AdminRepository $adminRepo;

    protected function setUp(): void
    {
        parent::setUp();

        $this->permissionService = $this->app->make(PermissionService::class);
        $this->adminRepo = $this->app->make(AdminRepository::class);
        $this->roleBuilder = resolve(RoleBuilder::class);

        $this->langInit();
    }

    /** @test */
    public function create_super_admin(): void
    {
        $this->roleBuilder->asSuperAdmin()->create();

        $this->assertCount(0, $this->adminRepo->getByRoleName(BaseRole::SUPER_ADMIN));

        app(SuperAdminSeeder::class)->run();

        $this->assertCount(1, $this->adminRepo->getByRoleName(BaseRole::SUPER_ADMIN));

//        self::assertEquals(
//            $this->permissionService
//                ->getPermissionsList(Admin::GUARD)
//                ->count(),
//            Permission::query()
//                ->whereGuardName(Admin::GUARD)
//                ->count()
//        );
    }
}
