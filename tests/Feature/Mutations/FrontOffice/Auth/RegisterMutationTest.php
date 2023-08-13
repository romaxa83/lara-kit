<?php

namespace Tests\Feature\Mutations\FrontOffice\Auth;

use App\GraphQL\Mutations\FrontOffice\Auth\RegisterMutation;
use App\Modules\Permissions\Models\Role;
use App\Modules\User\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Builders\Admins\AdminBuilder;
use Tests\Builders\Permissions\RoleBuilder;
use Tests\Builders\Users\UserBuilder;
use Tests\TestCase;
use Tests\Traits\Assert\AssertErrors;

class RegisterMutationTest extends TestCase
{
    use DatabaseTransactions;
    use AssertErrors;

    protected RoleBuilder $roleBuilder;
    protected UserBuilder $userBuilder;
    protected AdminBuilder $adminBuilder;

    public const MUTATION = RegisterMutation::NAME;

    protected array $data = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->roleBuilder = resolve(RoleBuilder::class);
        $this->userBuilder = resolve(UserBuilder::class);
        $this->adminBuilder = resolve(AdminBuilder::class);

        $this->data = [
            'name' => 'New User Name',
            'email' => 'new.admin.email@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ];

        $this->langInit();
        $this->passportInit();
    }

    /** @test */
    public function success_register()
    {
        /** @var $role Role */
        $this->roleBuilder->asUser()->create();

        $data = $this->data;
        $data['phone'] = '0954514992';
        $data['remember_me'] = 'true';

        $this->adminBuilder->phone($data['phone'])->create();

        $this->assertFalse(User::query()->where('email', $data['email'])->exists());

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

        $this->assertTrue(User::query()->where('email', $data['email'])->exists());
    }

    protected function getQueryStr(array $data): string
    {
        return sprintf(
            '
            mutation {
                %s (
                    input: {
                        name: "%s"
                        email: "%s"
                        password: "%s"
                        password_confirmation: "%s",
                        phone: "%s"
                        remember_me: %s
                    },
                ) {
                    token_type
                    access_expires_in
                    refresh_expires_in
                    access_token
                    refresh_token
                }
            }',
            self::MUTATION,
            data_get($data, 'name'),
            data_get($data, 'email'),
            data_get($data, 'password'),
            data_get($data, 'password_confirmation'),
            data_get($data, 'phone'),
            data_get($data, 'remember_me'),
        );
    }

    /** @test */
    public function success_create_only_required_field()
    {
        /** @var $role Role */
        $this->roleBuilder->asUser()->create();

        $data = $this->data;

        $this->postGraphQL([
            'query' => $this->getQueryStrOnlyRequiredField($data)
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

    protected function getQueryStrOnlyRequiredField(array $data): string
    {
        return sprintf(
            '
            mutation {
                %s (
                    input: {
                        name: "%s"
                        email: "%s"
                        password: "%s"
                        password_confirmation: "%s",

                    },
                ) {
                    token_type
                    access_expires_in
                    refresh_expires_in
                    access_token
                    refresh_token
                }
            }',
            self::MUTATION,
            data_get($data, 'name'),
            data_get($data, 'email'),
            data_get($data, 'password'),
            data_get($data, 'password_confirmation'),
        );
    }

    /**
     * @dataProvider validate
     * @test
     */
    public function validate_data($field, $value, $msgKey, $attributes = [])
    {
        $this->userBuilder
            ->email('test@test.com')
            ->phone('380959999999')
            ->create();

        /** @var $role Role */
        $this->roleBuilder->asUser()->create();

        $data = $this->data;
        $data['phone'] = '0954514992';
        $data['remember_me'] = 'false';

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
            ['name', null, 'validation.required', ['attribute' => 'name']],
            ['name', 't', 'validation.custom.name.name_rule', ['attribute' => 'Name']],
            ['email', null, 'validation.required', ['attribute' => 'email']],
            ['email', 'wrong-email', 'validation.email', ['attribute' => 'email']],
            ['email', 'test@test.com', 'validation.unique', ['attribute' => 'email']],
            ['password', null, 'validation.required', ['attribute' => 'password']],
            ['password', 55, 'validation.custom.password.password_rule'],
            ['password', 'pas', 'validation.custom.password.password_rule'],
            ['password', 'passwordee', 'validation.custom.password.password_rule'],
            ['password_confirmation', null, 'validation.confirmed', ['attribute' => 'password']],
            ['password_confirmation', 'wrong-password', 'validation.confirmed', ['attribute' => 'password']],
            ['phone', 'passwordee', 'validation.custom.phone.not_valid', ['value' => 'passwordee']],
            ['phone', '380959999999', 'validation.unique_phone'],
        ];
    }
}

