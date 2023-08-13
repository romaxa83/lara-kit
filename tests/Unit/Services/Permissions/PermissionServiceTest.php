<?php

namespace Tests\Unit\Services\Permissions;

use App\Modules\Admin\Models\Admin;
use Core\Permissions\Permission;
use Core\Permissions\PermissionGroup;
use Core\Services\Permissions\PermissionService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PermissionServiceTest extends TestCase
{
    use DatabaseTransactions;

    private PermissionService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(PermissionService::class);
    }

    public function test_it_get_groups_of_permissions(): void
    {
        $groups = $this->service->getGroupsFor(Admin::GUARD);
        $guard = Admin::GUARD;
        $count = count(config("grants.matrix.$guard.groups"));
        self::assertCount($count, $groups);

        $group = $groups->first();
        self::assertInstanceOf(PermissionGroup::class, $group);

        $permissions = $group->getPermissions();

        self::assertCount(4, $permissions);
        $permission = array_shift($permissions);

        self::assertInstanceOf(Permission::class, $permission);
    }


    public function test_it_get_super_admin_from_db(): void
    {
        $role = $this->service->firstOrCreateSuperAdminRole();

        self::assertEquals(config('permission.roles.super_admin'), $role->name);
    }
}
