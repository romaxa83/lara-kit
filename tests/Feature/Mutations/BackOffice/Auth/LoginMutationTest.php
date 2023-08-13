<?php

namespace Tests\Feature\Mutations\BackOffice\Auth;

use App\GraphQL\Mutations\BackOffice\Auth\LoginMutation;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Builders\Admins\AdminBuilder;
use Tests\TestCase;

class LoginMutationTest extends TestCase
{
    use DatabaseTransactions;

    public const MUTATION = LoginMutation::NAME;

    public AdminBuilder $adminBuilder;

    protected array $data = [];
    protected function setUp(): void
    {
        parent::setUp();

        $this->adminBuilder = resolve(AdminBuilder::class);

        $this->data = [
            'email' => 'new.admin.email@example.com',
            'password' => 'Password123',
        ];

        $this->passportInit();
        $this->langInit();
    }

    /** @test  */
    public function success_login(): void
    {
        $data = $this->data;
        $data['remember_me'] = 'true';

        $this->adminBuilder
            ->email($data['email'])
            ->password($data['password'])
            ->create();

        $this->postGraphQLBackOffice([
            'query' => $this->getQueryStr($data)
        ])
            ->assertJsonStructure([
                'data' => [
                    self::MUTATION => [
                        'token_type',
                        'access_expires_in',
                        'refresh_expires_in',
                        'access_token',
                        'refresh_token',
                    ],
                ]
            ])
        ;
    }

    /** @test  */
    public function fail_wrong_email(): void
    {
        $data = $this->data;
        $data['remember_me'] = 'true';

        $this->adminBuilder
            ->email('test@test.com')
            ->password($data['password'])
            ->create();

        $res = $this->postGraphQLBackOffice([
            'query' => $this->getQueryStr($data)
        ])
        ;

        self::assertResponseHasValidationMessage($res, 'input.password',[
            __('auth.failed')
        ]);
    }

    /**
     * @dataProvider validate
     * @test
     */
    public function validate_data($field, $value, $msgKey, $attributes = [])
    {
        $this->adminBuilder
            ->email('test@test.com')
            ->create();

        $data = $this->data;
        $data['email'] = 'test@test.com';
        $data['remember_me'] = 'false';

        $data[$field] = $value;

        $res = $this->postGraphQLBackOffice([
            'query' => $this->getQueryStr($data)
        ])
        ;

        $this->assertResponseHasValidationMessage($res, 'input.'.$field, [__($msgKey, $attributes)]);
    }

    public static function validate(): array
    {
        return [
            ['email', null, 'validation.required', ['attribute' => 'email']],
            ['email', 'wrong-email', 'validation.email', ['attribute' => 'email']],
            ['password', null, 'validation.required', ['attribute' => 'password']],
            ['password', 55, 'auth.failed'],
            ['password', 'pas', 'auth.failed'],
            ['password', 'passwordee', 'auth.failed'],
        ];
    }

    public static function getQueryStr(array $data): string
    {
        return sprintf(
            '
            mutation {
                %s (
                    input: {
                        email: "%s",
                        password: "%s",
                    }
                ) {
                    token_type
                    refresh_token
                    refresh_expires_in
                    access_expires_in
                    token_type
                    access_token
                }
            }',
            self::MUTATION,
            data_get($data, 'email'),
            data_get($data, 'password'),
        );
    }
}
