<?php

namespace Tests\Feature\Mutations\FrontOffice\Auth;

use App\GraphQL\Mutations\FrontOffice\Auth\LoginMutation;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Builders\Users\UserBuilder;
use Tests\TestCase;
use Tests\Traits\Assert\AssertErrors;

class LoginMutationTest extends TestCase
{
    use DatabaseTransactions;
    use AssertErrors;

    protected UserBuilder $userBuilder;

    public const MUTATION = LoginMutation::NAME;

    protected array $data = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->userBuilder = resolve(UserBuilder::class);

        $this->data = [
            'email' => 'new.admin.email@example.com',
            'password' => 'Password123',
        ];

        $this->langInit();
        $this->passportInit();
    }

    /** @test */
    public function success_login()
    {
        $data = $this->data;
        $data['remember_me'] = 'true';

        $this->userBuilder
            ->email($data['email'])
            ->password($data['password'])
            ->create();

        $this->postGraphQL([
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

    public static function getQueryStr(array $data): string
    {
        return sprintf(
            '
            mutation {
                %s (
                    input: {
                        email: "%s"
                        password: "%s"
                        remember_me: %s,
                    }
                ) {
                    token_type
                    access_expires_in
                    refresh_expires_in
                    access_token
                    refresh_token
                }
            }',
            self::MUTATION,
            data_get($data, 'email'),
            data_get($data, 'password'),
            data_get($data, 'remember_me'),
        );
    }

    /** @test */
    public function fail_wrong_email()
    {
        $data = $this->data;
        $data['remember_me'] = 'true';

        $this->userBuilder
            ->email('test@test.com')
            ->password($data['password'])
            ->create();

        $res = $this->postGraphQL([
            'query' => $this->getQueryStr($data)
        ])
        ;

        $this->assertResponseHasValidationMessage($res, 'input.password', [__('auth.failed')]);
    }

    /**
     * @dataProvider validate
     * @test
     */
    public function validate_data($field, $value, $msgKey, $attributes = [])
    {
        $this->userBuilder
            ->email('test@test.com')
            ->create();

        $data = $this->data;
        $data['email'] = 'test@test.com';
        $data['remember_me'] = 'false';

        $data[$field] = $value;

        $res = $this->postGraphQL([
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
}


