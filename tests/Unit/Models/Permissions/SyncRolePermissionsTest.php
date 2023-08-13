<?php

namespace Tests\Unit\Models\Permissions;

use App\Modules\Permissions\Models\Role;
use App\Modules\User\Models\User;
use App\Permissions\Users\UserCreatePermission;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SyncRolePermissionsTest extends TestCase
{
    use DatabaseTransactions;

    public function test_it_sync_permission_into_role()
    {
        $role = Role::factory()->create();
        $createdPermission = $role->permissions()->firstOrCreate(
            ['name' => UserCreatePermission::KEY, 'guard_name' => User::GUARD]
        );
        $role->refresh();
        $permissionList = $role->permissionList;
        $permission = array_shift($permissionList);

        self::assertEquals(UserCreatePermission::KEY, $permission);
        self::assertEquals($createdPermission->name, $permission);
    }
}
