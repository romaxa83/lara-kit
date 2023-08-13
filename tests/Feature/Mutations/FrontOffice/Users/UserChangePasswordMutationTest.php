<?php

namespace Tests\Feature\Mutations\FrontOffice\Users;

use App\GraphQL\Mutations\FrontOffice\Users\UserChangePasswordMutation;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Builders\Users\UserBuilder;
use Tests\TestCase;
use Tests\Traits\Assert\AssertNotification;

class UserChangePasswordMutationTest extends TestCase
{
    use DatabaseTransactions;
    use AssertNotification;

    public const MUTATION = UserChangePasswordMutation::NAME;

    protected UserBuilder $userBuilder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->langInit();

        $this->userBuilder = resolve(UserBuilder::class);
    }

    /** @test */
    public function success_change(): void
    {
        $model = $this->loginAsUser();

        $newPassword = 'newPassword123';

        $this->assertFalse(password_verify($newPassword, $model->password));

        $this->postGraphQL([
            'query' => $this->getQueryStr([
                'current' => 'password',
                'password' => $newPassword,
                'password_confirmation' => $newPassword
            ])
        ])
            ->assertOk()
            ->assertJson([
                'data' => [
                    self::MUTATION => [
                        'success' => true,
                        'message' => __('messages.user.actions.change_password.success'),
                    ]
                ]
            ])
        ;

        $this->assertTrue(password_verify($newPassword, $model->password));
    }

    protected function getQueryStr(array $data): string
    {
        return sprintf(
            '
            mutation {
                %s (
                    input: {
                        current: "%s"
                        password: "%s"
                        password_confirmation: "%s"
                    },
                ) {
                    success
                    message
                }
            }',
            self::MUTATION,
            $data['current'],
            $data['password'],
            $data['password_confirmation'],
        );
    }

    /**
     * @dataProvider validate
     * @test
     */
    public function validate_data($field, $value, $msgKey, $attributes = [])
    {
        $this->loginAsUser();

        $data = [
            'current' => 'password',
            'password' => 'newPassword123',
            'password_confirmation' => 'newPassword123'
        ];

        $data[$field] = $value;

        $res = $this->postGraphQL([
            'query' => $this->getQueryStr($data)
        ])
        ;

        if($field == 'password_confirmation'){
            $field = 'password';
        }

        $this->assertResponseHasValidationMessage($res, 'input.'.$field, [__($msgKey, $attributes)]);
    }

    public static function validate(): array
    {
        return [
            ['current', null, 'validation.required', ['attribute' => 'input.current']],
            ['current', 'new-password', 'auth.password'],
            ['password', null, 'validation.required', ['attribute' => 'password']],
            ['password', 55, 'validation.custom.password.password_rule'],
            ['password', 'pas', 'validation.custom.password.password_rule'],
            ['password', 'passwordee', 'validation.custom.password.password_rule'],
            ['password_confirmation', null, 'validation.confirmed', ['attribute' => 'password']],
            ['password_confirmation', 'wrong-password', 'validation.confirmed', ['attribute' => 'password']],
        ];
    }

    /** @test */
    public function not_auth(): void
    {
        $res = $this->postGraphQL([
            'query' => $this->getQueryStr([
                'current' => 'password',
                'password' => 'newPassword123',
                'password_confirmation' => 'newPassword123'
            ])
        ])
        ;

        $this->assertUnauthorized($res);
    }
}
