<?php

namespace Tests\Feature\Mutations\FrontOffice\Auth;

use App\GraphQL\Mutations\FrontOffice\Auth\LoginMutation;
use App\GraphQL\Mutations\FrontOffice\Auth\TokenRefreshMutation;
use App\Modules\User\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Builders\Users\UserBuilder;
use Tests\TestCase;

class TokenRefreshMutationTest extends TestCase
{
    use DatabaseTransactions;

    public const MUTATION = TokenRefreshMutation::NAME;

    public UserBuilder $userBuilder;
    protected function setUp(): void
    {
        parent::setUp();

        $this->userBuilder = resolve(UserBuilder::class);

        $this->passportInit();
        $this->langInit();
    }

    /** @test */
    public function success_refresh_token(): void
    {
        /** @var $model User */
        $model = $this->userBuilder->create();

        $this->assertDatabaseHas(User::TABLE, ['email' => $model->email]);

        $res = $this->postGraphQL([
            'query' => LoginMutationTest::getQueryStr([
                'email' => $model->email,
                'password' => 'password',
                'remember_me' => 'true'
            ])
        ])
        ;

        [LoginMutation::NAME => $data] = $res->json('data');

        $res = $this->postGraphQL([
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
        /** @var $model User */
        $model = $this->userBuilder->create();

        $this->assertDatabaseHas(User::TABLE, ['email' => $model->email]);

        $res = $this->postGraphQL([
            'query' => LoginMutationTest::getQueryStr([
                'email' => $model->email,
                'password' => 'password',
                'remember_me' => 'true'
            ])
        ]);

        [LoginMutation::NAME => $data] = $res->json('data');

        $res = $this->postGraphQL([
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

