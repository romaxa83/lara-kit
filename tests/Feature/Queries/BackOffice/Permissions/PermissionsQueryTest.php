<?php

namespace Tests\Feature\Queries\BackOffice\Permissions;

use App\GraphQL\Queries\BackOffice;
use App\GraphQL\Queries\BackOffice\Permissions\PermissionsQuery;
use App\Modules\Permissions\Enums\Guard;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Builders\Admins\AdminBuilder;
use Tests\Builders\Permissions\PermissionBuilder;
use Tests\Builders\Permissions\RoleBuilder;
use Tests\TestCase;

class PermissionsQueryTest extends TestCase
{
    use DatabaseTransactions;

    public const QUERY = PermissionsQuery::NAME;

    protected AdminBuilder $adminBuilder;
    protected PermissionBuilder $permissionBuilder;
    protected RoleBuilder $roleBuilder;

    public function setUp(): void
    {
        parent::setUp();
        $this->adminBuilder = resolve(AdminBuilder::class);
        $this->permissionBuilder = resolve(PermissionBuilder::class);
        $this->roleBuilder = resolve(RoleBuilder::class);

        $this->langInit();
    }

    /** @test */
    public function success_get(): void
    {
        $this->loginAsSuperAdmin();

        $this->postGraphQLBackOffice([
            'query' => $this->getQueryStr(Guard::ADMIN)
        ])
            ->assertJson([
                'data' => [
                    self::QUERY => [
                        [
                            'key' => 'admin',
                            'name' => 'Admins',
                            'position' => 0,
                            'permissions' => [
                                [
                                    'key' => 'admin.list',
                                    'name' => 'List',
                                    'position' => 1,
                                ],
                                [
                                    'key' => 'admin.create',
                                    'name' => 'Create',
                                    'position' => 2,
                                ],
                                [
                                    'key' => 'admin.update',
                                    'name' => 'Update',
                                    'position' => 3,
                                ],
                                [
                                    'key' => 'admin.delete',
                                    'name' => 'Delete',
                                    'position' => 4,
                                ]
                            ]
                        ]
                    ]
                ]
            ])
        ;
    }

    protected function getQueryStr(string $value): string
    {
        return sprintf(
            '
            {
                %s (guard: %s) {
                    key
                    name
                    position
                    permissions {
                        key
                        name
                        position
                    }
                }
            }',
            self::QUERY,
            $value
        );
    }
}
