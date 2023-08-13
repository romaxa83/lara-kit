<?php

namespace Tests\Feature\Mutations\BackOffice\Users;

use App\GraphQL\Mutations\BackOffice\Users\UserDeleteMutation;
use App\Modules\Permissions\Enums\Guard;
use App\Modules\User\Models\User;
use App\Permissions\Users\UserDeletePermission;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Builders\Admins\AdminBuilder;
use Tests\Builders\Permissions\PermissionBuilder;
use Tests\Builders\Users\UserBuilder;
use Tests\TestCase;
use Tests\Traits\Assert\AssertErrors;

class UserDeleteMutationTest extends TestCase
{
    use DatabaseTransactions;
    use AssertErrors;

    protected AdminBuilder $adminBuilder;
    protected UserBuilder $userBuilder;
    protected PermissionBuilder $permissionBuilder;

    public const MUTATION = UserDeleteMutation::NAME;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminBuilder = resolve(AdminBuilder::class);
        $this->userBuilder = resolve(UserBuilder::class);
        $this->permissionBuilder = resolve(PermissionBuilder::class);

        $this->langInit();
    }

    /** @test */
    public function success_delete_one()
    {
        $this->loginAsSuperAdmin();

        $model_1 = $this->userBuilder->create();

        $data = [$model_1->id];

        $this->postGraphQLBackOffice([
            'query' => $this->getQueryStr($data)
        ])
            ->assertJson([
                'data' => [
                    self::MUTATION => [
                        'message' => __('messages.user.actions.delete.success.one_entity'),
                        'success' => true
                    ],
                ]
            ])
        ;

        $this->assertEquals(0, User::query()->whereIn('id', $data)->count());
        $this->assertEquals(1, User::query()->withTrashed()->whereIn('id', $data)->count());
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

        $model_1 = $this->userBuilder->create();
        $model_2 = $this->userBuilder->create();
        $model_3 = $this->userBuilder->create();

        $data = [$model_1->id, $model_2->id, $model_3->id];

        $this->postGraphQLBackOffice([
            'query' => $this->getQueryStrMany($data)
        ])
            ->assertJson([
                'data' => [
                    self::MUTATION => [
                        'message' => __('messages.user.actions.delete.success.many_entity'),
                        'success' => true
                    ],
                ]
            ])
        ;

        $this->assertEquals(0, User::query()->whereIn('id', $data)->count());
        $this->assertEquals(3, User::query()->withTrashed()->whereIn('id', $data)->count());
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
            ->name(UserDeletePermission::KEY)->create();
        $this->loginAsAdmin(null, $perm);

        $model_1 = $this->userBuilder->create();

        $data = [$model_1->id];

        $this->postGraphQLBackOffice([
            'query' => $this->getQueryStr($data)
        ])
            ->assertJson([
                'data' => [
                    self::MUTATION => [
                        'message' => __('messages.user.actions.delete.success.one_entity'),
                        'success' => true
                    ],
                ]
            ]);

        $this->assertEquals(0, User::query()->whereIn('id', $data)->count());
        $this->assertEquals(1, User::query()->withTrashed()->whereIn('id', $data)->count());
    }

    /** @test */
    public function fail_not_exist_user()
    {
        $this->loginAsSuperAdmin();

        $model = $this->userBuilder->create();

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
        $model_1 = $this->userBuilder->create();

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

        $model_1 = $this->userBuilder->create();

        $data = [$model_1->id];

        $res = $this->postGraphQLBackOffice([
            'query' => $this->getQueryStr($data)
        ]);

        $this->assertPermission($res);
    }
}
