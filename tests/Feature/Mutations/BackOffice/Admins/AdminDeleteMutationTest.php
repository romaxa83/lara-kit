<?php

namespace Tests\Feature\Mutations\BackOffice\Admins;

use App\GraphQL\Mutations\BackOffice\Admins\AdminDeleteMutation;
use App\Modules\Admin\Models\Admin;
use App\Modules\Permissions\Enums\Guard;
use App\Permissions\Admins\AdminDeletePermission;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Builders\Admins\AdminBuilder;
use Tests\Builders\Permissions\PermissionBuilder;
use Tests\TestCase;
use Tests\Traits\Assert\AssertErrors;

class AdminDeleteMutationTest extends TestCase
{
    use DatabaseTransactions;
    use AssertErrors;

    protected AdminBuilder $adminBuilder;
    protected PermissionBuilder $permissionBuilder;

    public const MUTATION = AdminDeleteMutation::NAME;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminBuilder = resolve(AdminBuilder::class);
        $this->permissionBuilder = resolve(PermissionBuilder::class);

        $this->langInit();
    }

    /** @test */
    public function success_delete_one()
    {
        $this->loginAsSuperAdmin();

        $model_1 = $this->adminBuilder->create();

       $data = [$model_1->id];

        $this->postGraphQLBackOffice([
            'query' => $this->getQueryStr($data)
        ])
            ->assertJson([
                'data' => [
                    self::MUTATION => [
                        'message' => __('messages.admin.actions.delete.success.one_entity'),
                        'success' => true
                    ],
                ]
            ])
        ;

        $this->assertEquals(0, Admin::query()->whereIn('id', $data)->count());
        $this->assertEquals(1, Admin::query()->withTrashed()->whereIn('id', $data)->count());
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

        $model_1 = $this->adminBuilder->create();
        $model_2 = $this->adminBuilder->create();
        $model_3 = $this->adminBuilder->create();

        $data = [$model_1->id, $model_2->id, $model_3->id];

        $this->postGraphQLBackOffice([
            'query' => $this->getQueryStrMany($data)
        ])
            ->assertJson([
                'data' => [
                    self::MUTATION => [
                        'message' => __('messages.admin.actions.delete.success.many_entity'),
                        'success' => true
                    ],
                ]
            ])
        ;

        $this->assertEquals(0, Admin::query()->whereIn('id', $data)->count());
        $this->assertEquals(3, Admin::query()->withTrashed()->whereIn('id', $data)->count());
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
            ->name(AdminDeletePermission::KEY)->create();
        $this->loginAsAdmin(null, $perm);

        $model_1 = $this->adminBuilder->create();

        $data = [$model_1->id];

        $this->postGraphQLBackOffice([
            'query' => $this->getQueryStr($data)
        ])
            ->assertJson([
                'data' => [
                    self::MUTATION => [
                        'message' => __('messages.admin.actions.delete.success.one_entity'),
                        'success' => true
                    ],
                ]
            ]);

        $this->assertEquals(0, Admin::query()->whereIn('id', $data)->count());
        $this->assertEquals(1, Admin::query()->withTrashed()->whereIn('id', $data)->count());
    }

    /** @test */
    public function fail_cant_delete_self()
    {
        $perm = $this->permissionBuilder
            ->guard(Guard::ADMIN())
            ->name(AdminDeletePermission::KEY)->create();
        $model = $this->loginAsAdmin(null, $perm);

        $model_1 = $this->adminBuilder->create();
        $model_2 = $this->adminBuilder->create();

        $data = [$model->id, $model_1->id, $model_2->id];

        $this->postGraphQLBackOffice([
            'query' => $this->getQueryStrMany($data)
        ])
            ->assertJson([
                'data' => [
                    self::MUTATION => [
                        'message' => __('messages.admin.actions.delete.fail.reasons.by_myself'),
                        'success' => false
                    ],
                ]
            ]);

        $this->assertEquals(3, Admin::query()->whereIn('id', $data)->count());
    }

    /** @test */
    public function fail_cant_delete_super_admin()
    {
        $perm = $this->permissionBuilder
            ->guard(Guard::ADMIN())
            ->name(AdminDeletePermission::KEY)->create();
        $this->loginAsAdmin(null, $perm);

        $model_1 = $this->adminBuilder->asSuperAdmin()->create();
        $model_2 = $this->adminBuilder->create();
        $model_3 = $this->adminBuilder->create();

        $data = [$model_3->id, $model_1->id, $model_2->id];

        $this->postGraphQLBackOffice([
            'query' => $this->getQueryStrMany($data)
        ])
            ->assertJson([
                'data' => [
                    self::MUTATION => [
                        'message' => __('messages.admin.actions.delete.fail.reasons.super_admin'),
                        'success' => false
                    ],
                ]
            ]);

        $this->assertEquals(3, Admin::query()->whereIn('id', $data)->count());
    }

    /** @test */
    public function fail_not_exist_admin()
    {
        $this->loginAsSuperAdmin();

        $model = $this->adminBuilder->create();

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
        $model_1 = $this->adminBuilder->create();

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

        $model_1 = $this->adminBuilder->create();

        $data = [$model_1->id];

        $res = $this->postGraphQLBackOffice([
            'query' => $this->getQueryStr($data)
        ]);

        $this->assertPermission($res);
    }
}
