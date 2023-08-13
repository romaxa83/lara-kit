<?php

namespace Tests\Feature\Mutations\BackOffice\Permissions;

use App\GraphQL\Mutations\BackOffice\Permission\RoleDeleteMutation;
use App\Modules\Permissions\Enums\Guard;
use App\Modules\Permissions\Models\Role;
use App\Permissions\Roles\RoleDeletePermission;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Builders\Admins\AdminBuilder;
use Tests\Builders\Permissions\PermissionBuilder;
use Tests\Builders\Permissions\RoleBuilder;
use Tests\TestCase;
use Tests\Traits\Assert\AssertErrors;

class RoleDeleteMutationTest extends TestCase
{
    use DatabaseTransactions;
    use AssertErrors;

    protected AdminBuilder $adminBuilder;
    protected PermissionBuilder $permissionBuilder;
    protected RoleBuilder $roleBuilder;

    public const MUTATION = RoleDeleteMutation::NAME;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminBuilder = resolve(AdminBuilder::class);
        $this->permissionBuilder = resolve(PermissionBuilder::class);
        $this->roleBuilder = resolve(RoleBuilder::class);

        $this->langInit();
    }

    /** @test */
    public function success_delete_one()
    {
        $this->loginAsSuperAdmin();

        $model_1 = $this->roleBuilder->create();

        $data = [$model_1->id];

        $this->postGraphQLBackOffice([
            'query' => $this->getQueryStr($data)
        ])
            ->assertJson([
                'data' => [
                    self::MUTATION => [
                        'message' => __('messages.role.actions.delete.success.one_entity'),
                        'success' => true
                    ],
                ]
            ])
        ;

        $this->assertEquals(0, Role::query()->whereIn('id', $data)->count());
    }

    protected function getQueryStr(array $data): string
    {
        return sprintf(
            '
            mutation {
                %s (
                    ids: ["%s"],
                ) {
                    message
                    success
                }
            }',
            self::MUTATION,
            data_get($data, '0'),
        );
    }

    /** @test */
    public function success_delete_many()
    {
        $this->loginAsSuperAdmin();

        $model_1 = $this->roleBuilder->create();
        $model_2 = $this->roleBuilder->create();
        $model_3 = $this->roleBuilder->create();

        $data = [$model_1->id, $model_2->id, $model_3->id];

        $this->postGraphQLBackOffice([
            'query' => $this->getQueryStrMany($data)
        ])
            ->assertJson([
                'data' => [
                    self::MUTATION => [
                        'message' => __('messages.role.actions.delete.success.many_entity'),
                        'success' => true
                    ],
                ]
            ])
        ;

        $this->assertEquals(0, Role::query()->whereIn('id', $data)->count());
    }

    protected function getQueryStrMany(array $data): string
    {
        return sprintf(
            '
            mutation {
                %s (
                    ids: ["%s", "%s", "%s"],
                ) {
                    message
                    success
                }
            }',
            self::MUTATION,
            data_get($data, '0'),
            data_get($data, '1'),
            data_get($data, '2'),
        );
    }

    /** @test */
    public function success_delete_as_admin()
    {
        $perm = $this->permissionBuilder
            ->guard(Guard::ADMIN())
            ->name(RoleDeletePermission::KEY)->create();
        $this->loginAsAdmin(null, $perm);

        $model_1 = $this->roleBuilder->create();

        $data = [$model_1->id];

        $this->postGraphQLBackOffice([
            'query' => $this->getQueryStr($data)
        ])
            ->assertJson([
                'data' => [
                    self::MUTATION => [
                        'message' => __('messages.role.actions.delete.success.one_entity'),
                        'success' => true
                    ],
                ]
            ]);
    }

    /** @test */
    public function fail_role_attached()
    {
        $this->loginAsSuperAdmin();

        $model = $this->roleBuilder->create();

        $this->adminBuilder->role($model)->create();

        $data = [$model->id];

        $this->postGraphQLBackOffice([
            'query' => $this->getQueryStr($data)
        ])
            ->assertJson([
                'data' => [
                    self::MUTATION => [
                        'message' => __('exceptions.role.not_cant_delete_role'),
                        'success' => false
                    ],
                ]
            ]);

        $this->assertEquals(1, Role::query()->whereIn('id', $data)->count());
    }

    /** @test */
    public function fail_not_exist_model()
    {
        $this->loginAsSuperAdmin();

        $model = $this->roleBuilder->create();

        $data = [$model->id + 1];

        $res = $this->postGraphQLBackOffice([
            'query' => $this->getQueryStr($data)
        ])
        ;

        $this->assertResponseHasValidationMessage($res,
            'ids.0',
            [__('validation.exists', ['attribute' => 'ids.0'])]
        );
    }

    /** @test */
    public function not_auth()
    {
        $model_1 = $this->roleBuilder->create();

        $data = [$model_1->id];

        $res = $this->postGraphQLBackOffice([
            'query' => $this->getQueryStr($data)
        ])
        ;

        $this->assertUnauthorized($res);
    }

    /** @test */
    public function not_perm()
    {
        $this->loginAsAdmin();

        $model_1 = $this->roleBuilder->create();

        $data = [$model_1->id];

        $res = $this->postGraphQLBackOffice([
            'query' => $this->getQueryStr($data)
        ]);

        $this->assertPermission($res);
    }
}

