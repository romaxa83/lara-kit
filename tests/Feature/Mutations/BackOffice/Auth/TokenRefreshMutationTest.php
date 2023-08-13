<?php

namespace Tests\Feature\Mutations\BackOffice\Auth;

use App\GraphQL\Mutations\BackOffice\Auth\LoginMutation;
use App\GraphQL\Mutations\BackOffice\Auth\TokenRefreshMutation;
use App\Modules\Admin\Models\Admin;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Builders\Admins\AdminBuilder;
use Tests\TestCase;

class TokenRefreshMutationTest extends TestCase
{
    use DatabaseTransactions;

    public const MUTATION = TokenRefreshMutation::NAME;

    public AdminBuilder $adminBuilder;
    protected function setUp(): void
    {
        parent::setUp();

        $this->adminBuilder = resolve(AdminBuilder::class);

        $this->passportInit();
        $this->langInit();
    }

    /** @test */
    public function success_refresh_token(): void
    {
        /** @var $model Admin */
        $model = $this->adminBuilder->create();

        $this->assertDatabaseHas(Admin::TABLE, ['email' => $model->email]);

        $res = $this->postGraphQLBackOffice([
            'query' => LoginMutationTest::getQueryStr([
                'email' => $model->email,
                'password' => 'password'
            ])
        ])
        ;

        [LoginMutation::NAME => $data] = $res->json('data');

        $res = $this->postGraphQLBackOffice([
            'query' => $this->getQueryStr($data['refresh_token'])
        ])
            ->assertOk();

        [self::MUTATION => $data] = $res->json('data');

        self::assertArrayHasKey('refresh_token', $data);
        self::assertArrayHasKey('access_expires_in', $data);
        self::assertArrayHasKey('token_type', $data);
        self::assertArrayHasKey('access_token', $data);
    }

    /** @test */
    public function fail_wrong_refresh_token(): void
    {
        /** @var $model Admin */
        $model = $this->adminBuilder->create();

        $this->assertDatabaseHas(Admin::TABLE, ['email' => $model->email]);

        $res = $this->postGraphQLBackOffice([
            'query' => LoginMutationTest::getQueryStr([
                'email' => $model->email,
                'password' => 'password'
            ])
        ]);

        [LoginMutation::NAME => $data] = $res->json('data');

        $res = $this->postGraphQLBackOffice([
            'query' => $this->getQueryStr($data['refresh_token'] . 'wrong')
        ])
            ->assertOk();

       self::assertServerError($res);
       self::assertGraphQlDebugMessage($res, "The refresh token is invalid.");
    }

    public static function getQueryStr(string $value): string
    {
        return sprintf(
            '
            mutation {
                %s (
                    refresh_token: "%s"
                ) {
                    token_type
                    refresh_token
                    access_expires_in
                    token_type
                    access_token
                }
            }',
            self::MUTATION,
            $value,
        );
    }
}
