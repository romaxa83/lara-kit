<?php

namespace Tests\Feature\Mutations\BackOffice\Admins;

use App\GraphQL\Mutations\BackOffice\Admins\AdminCreateMutation;
use App\Modules\Localization\Models\Language;
use App\Modules\Permissions\Enums\Guard;
use App\Modules\Permissions\Models\Role;
use App\Permissions\Admins\AdminCreatePermission;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Builders\Admins\AdminBuilder;
use Tests\Builders\Localization\LanguageBuilder;
use Tests\Builders\Permissions\PermissionBuilder;
use Tests\Builders\Permissions\RoleBuilder;
use Tests\Builders\Users\UserBuilder;
use Tests\TestCase;
use Tests\Traits\Assert\AssertErrors;

class AdminCreateMutationTest extends TestCase
{
    use DatabaseTransactions;
    use AssertErrors;

    protected RoleBuilder $roleBuilder;
    protected PermissionBuilder $permissionBuilder;
    protected AdminBuilder $adminBuilder;
    protected LanguageBuilder $langBuilder;
    protected UserBuilder $userBuilder;

    public const MUTATION = AdminCreateMutation::NAME;

    protected array $data = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->roleBuilder = resolve(RoleBuilder::class);
        $this->adminBuilder = resolve(AdminBuilder::class);
        $this->langBuilder = resolve(LanguageBuilder::class);
        $this->permissionBuilder = resolve(PermissionBuilder::class);
        $this->userBuilder = resolve(UserBuilder::class);

        $this->data = [
            'name' => 'New Admin Name',
            'email' => 'new.admin.email@example.com',
            'password' => 'Password123',
        ];

        $this->langInit();
    }

    /** @test */
    public function success_create()
    {
        $this->loginAsSuperAdmin();

        /** @var $role Role */
        $role = $this->roleBuilder->create();
        /** @var $lang Language */
        $lang = $this->langBuilder->slug('uk')->create();

        $data = $this->data;
        $data['role'] = $role->id;
        $data['lang'] = $lang->slug;
        $data['phone'] = '0954514992';

        $this->postGraphQLBackOffice([
            'query' => $this->getQueryStr($data)
        ])
            ->assertJsonStructure([
                'data' => [
                    self::MUTATION => [
                        'id',
                    ],
                ]
            ])
            ->assertJson([
                'data' => [
                    self::MUTATION => [
                        'name' => data_get($data, 'name'),
                        'email' => data_get($data, 'email'),
                        'email_verified' => false,
                        'lang' => data_get($data, 'lang'),
                        'phone' => data_get($data, 'phone'),
                        'phone_verified' => false,
                        'role' => [
                            'id' => $role->id
                        ],
                    ],
                ]
            ])
            ->assertJsonCount(1, 'data.'.self::MUTATION.'.roles')
        ;
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
                        lang: "%s"
                        role: "%s"
                        phone: "%s"
                    },
                ) {
                    id
                    name
                    email
                    email_verified
                    lang
                    phone
                    phone_verified
                    role {
                        id
                    }
                    roles {
                        id
                    }
                }
            }',
            self::MUTATION,
            data_get($data, 'name'),
            data_get($data, 'email'),
            data_get($data, 'password'),
            data_get($data, 'lang'),
            data_get($data, 'role'),
            data_get($data, 'phone'),
        );
    }

    /** @test */
    public function success_create_only_required_field()
    {
        $this->loginAsSuperAdmin();

        /** @var $role Role */
        $role = $this->roleBuilder->create();

        $data = $this->data;
        $data['role'] = $role->id;

        $this->postGraphQLBackOffice([
            'query' => $this->getQueryStrOnlyRequiredField($data)
        ])
            ->assertJson([
                'data' => [
                    self::MUTATION => [
                        'lang' => default_lang()->slug,
                        'phone' => null,
                        'phone_verified' => false,
                    ],
                ]
            ])
        ;
    }

    /** @test */
    public function success_create_if_exist_phone_another_entity()
    {
        $phone = '380955556655';
        $this->userBuilder->phone($phone)->create();

        $this->loginAsSuperAdmin();

        /** @var $role Role */
        $role = $this->roleBuilder->create();
        /** @var $lang Language */
        $lang = $this->langBuilder->slug('uk')->create();

        $data = $this->data;
        $data['role'] = $role->id;
        $data['lang'] = $lang->slug;
        $data['phone'] = $phone;

        $this->postGraphQLBackOffice([
            'query' => $this->getQueryStr($data)
        ])
            ->assertJson([
                'data' => [
                    self::MUTATION => [
                        'phone' => $phone,
                    ],
                ]
            ])
        ;
    }

    /** @test */
    public function success_create_as_admin()
    {
        $perm = $this->permissionBuilder
            ->guard(Guard::ADMIN())
            ->name(AdminCreatePermission::KEY)->create();
        $this->loginAsAdmin(null, $perm);

        /** @var $role Role */
        $role = $this->roleBuilder->create();

        $data = $this->data;
        $data['role'] = $role->id;

        $this->postGraphQLBackOffice([
            'query' => $this->getQueryStrOnlyRequiredField($data)
        ])
            ->assertJson([
                'data' => [
                    self::MUTATION => [
                        'lang' => default_lang()->slug,
                        'phone' => null,
                        'phone_verified' => false,
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
                        role: "%s"
                    },
                ) {
                    id
                    lang
                    phone
                    phone_verified
                }
            }',
            self::MUTATION,
            data_get($data, 'name'),
            data_get($data, 'email'),
            data_get($data, 'password'),
            data_get($data, 'role'),
        );
    }

    /**
     * @dataProvider validate
     * @test
     */
    public function validate_data($field, $value, $msgKey, $attributes = [])
    {
        $this->loginAsSuperAdmin();

        $this->adminBuilder
            ->email('test@test.com')
            ->phone('380959999999')
            ->create();

        /** @var $role Role */
        $role = $this->roleBuilder->create();
        /** @var $lang Language */
        $lang = $this->langBuilder->slug('uk')->create();

        $data = $this->data;
        $data['role'] = $role->id;
        $data['lang'] = $lang->slug;
        $data['phone'] = '0954514992';

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
            ['name', null, 'validation.required', ['attribute' => 'name']],
            ['name', 't', 'validation.custom.name.name_rule', ['attribute' => 'Name']],
            ['email', null, 'validation.required', ['attribute' => 'email']],
            ['email', 'wrong-email', 'validation.email', ['attribute' => 'email']],
            ['email', 'test@test.com', 'validation.unique', ['attribute' => 'email']],
            ['password', null, 'validation.required', ['attribute' => 'password']],
            ['password', 55, 'validation.custom.password.password_rule'],
            ['password', 'pas', 'validation.custom.password.password_rule'],
            ['password', 'passwordee', 'validation.custom.password.password_rule'],
            ['phone', 'passwordee', 'validation.custom.phone.not_valid', ['value' => 'passwordee']],
            ['phone', '380959999999', 'validation.unique_phone'],
            ['role', null, 'validation.required', ['attribute' => 'role']],
            ['role', 99999, 'validation.exists', ['attribute' => 'role']],
            ['lang', 99999, 'validation.exists', ['attribute' => 'lang']],
        ];
    }

    /** @test */
    public function not_auth()
    {
        /** @var $role Role */
        $role = $this->roleBuilder->create();

        $data = $this->data;
        $data['role'] = $role->id;

        $res = $this->postGraphQLBackOffice([
            'query' => $this->getQueryStrOnlyRequiredField($data)
        ])
        ;

        $this->assertUnauthorized($res);
    }

    /** @test */
    public function not_perm()
    {
        $this->loginAsAdmin();

        /** @var $role Role */
        $role = $this->roleBuilder->create();

        $data = $this->data;
        $data['role'] = $role->id;

        $res = $this->postGraphQLBackOffice([
            'query' => $this->getQueryStrOnlyRequiredField($data)
        ])
        ;

        $this->assertPermission($res);
    }
}
