<?php

namespace Tests\Feature\Mutations\BackOffice\Admins;

use App\GraphQL\Mutations\BackOffice\Admins\AdminUpdateMutation;
use App\Modules\Admin\Models\Admin;
use App\Modules\Localization\Models\Language;
use App\Modules\Permissions\Enums\Guard;
use App\Modules\Permissions\Models\Role;
use App\Modules\Utils\Phones\ValueObject\Phone;
use App\Permissions\Admins\AdminUpdatePermission;
use App\ValueObjects\Email;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Builders\Admins\AdminBuilder;
use Tests\Builders\Localization\LanguageBuilder;
use Tests\Builders\Permissions\PermissionBuilder;
use Tests\Builders\Permissions\RoleBuilder;
use Tests\Builders\Users\UserBuilder;
use Tests\TestCase;
use Tests\Traits\Assert\AssertErrors;

class AdminUpdateMutationTest extends TestCase
{
    use DatabaseTransactions;
    use AssertErrors;

    protected RoleBuilder $roleBuilder;
    protected PermissionBuilder $permissionBuilder;
    protected AdminBuilder $adminBuilder;
    protected LanguageBuilder $langBuilder;
    protected UserBuilder $userBuilder;

    public const MUTATION = AdminUpdateMutation::NAME;

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
    public function success_update()
    {
        $phone = '380954555566';
        $this->userBuilder->phone($phone)->create();

        $this->loginAsSuperAdmin();

        /** @var $role Role */
        $role = $this->roleBuilder->create();
        /** @var $lang Language */
        $lang = $this->langBuilder->slug('uk')->create();
        $lang_2 = $this->langBuilder->slug('fr')->create();

        /** @var $model Admin */
        $model = $this->adminBuilder
            ->phone(verify: true)
            ->email(verify: true)
            ->lang($lang)
            ->create();

        $data = $this->data;
        $data['id'] = $model->id;
        $data['role'] = $role->id;
        $data['lang'] = $lang_2->slug;
        $data['phone'] = $phone;

        $this->assertNotEquals($model->name, $data['name']);
        $this->assertFalse($model->email->compare(new Email($data['email'])));
        $this->assertTrue($model->isEmailVerified());
        $this->assertNotEquals($model->role->id, $data['role']);
        $this->assertNotEquals($model->lang, $data['lang']);
        $this->assertFalse(password_verify($data['password'], $model->password));
        $this->assertFalse($model->phone->phone->compare(new Phone($data['phone'])));
        $this->assertTrue($model->isPhoneVerified());

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
                        'id' => $model->id,
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

        $model->refresh();

        $this->assertTrue(password_verify($data['password'], $model->password));
    }

    /** @test */
    public function success_update_but_not_phone()
    {
        $this->loginAsSuperAdmin();

        /** @var $role Role */
        $role = $this->roleBuilder->create();
        /** @var $lang Language */
        $lang = $this->langBuilder->slug('uk')->create();
        $lang_2 = $this->langBuilder->slug('fr')->create();

        /** @var $model Admin */
        $model = $this->adminBuilder
            ->phone(verify: true)
            ->email(verify: true)
            ->lang($lang)
            ->create();

        $data = $this->data;
        $data['id'] = $model->id;
        $data['role'] = $role->id;
        $data['lang'] = $lang_2->slug;
        $data['phone'] = $model->phone->phone->getValue();

        $this->assertTrue($model->phone->phone->compare(new Phone($data['phone'])));
        $this->assertTrue($model->isPhoneVerified());

        $this->postGraphQLBackOffice([
            'query' => $this->getQueryStr($data)
        ])
            ->assertJson([
                'data' => [
                    self::MUTATION => [
                        'id' => $model->id,
                        'phone' => data_get($data, 'phone'),
                        'phone_verified' => true,
                    ],
                ]
            ])
        ;
    }

    protected function getQueryStr(array $data): string
    {
        return sprintf(
            '
            mutation {
                %s (
                    id: "%s"
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
                    phone
                    phone_verified
                    lang
                    role {
                        id
                    }
                    roles {
                        id
                    }
                }
            }',
            self::MUTATION,
            data_get($data, 'id'),
            data_get($data, 'name'),
            data_get($data, 'email'),
            data_get($data, 'password'),
            data_get($data, 'lang'),
            data_get($data, 'role'),
            data_get($data, 'phone'),
        );
    }

    /** @test */
    public function success_update_only_required_field()
    {
        $this->loginAsSuperAdmin();

        /** @var $lang Language */
        $lang = $this->langBuilder->slug('uk')->create();
        /** @var $model Admin */
        $model = $this->adminBuilder
            ->email(verify: true)
            ->phone(verify: true)
            ->lang($lang)->create();

        $data = $this->data;
        $data['id'] = $model->id;
        $data['role'] = $model->role->id;
        $data['email'] = $model->email->getValue();
        $data['phone'] = $model->phone->phone->getValue();

        $this->assertNotEquals($model->name, $data['name']);
        $this->assertEquals($model->email, $data['email']);
        $this->assertTrue($model->isEmailVerified());

        $this->postGraphQLBackOffice([
            'query' => $this->getQueryStrOnlyRequiredField($data)
        ])
            ->assertJson([
                'data' => [
                    self::MUTATION => [
                        'id' => $model->id,
                        'name' => $data['name'],
                        'email' => $data['email'],
                        'email_verified' => true,
                        'lang' => $model->lang,
                        'phone' => $model->phone->phone->getValue(),
                        'phone_verified' => true,
                        'role' => [
                            'id' => $model->role->id
                        ],
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
            ->name(AdminUpdatePermission::KEY)->create();
        $this->loginAsAdmin(null, $perm);

        /** @var $model Admin */
        $model = $this->adminBuilder->create();

        $data = $this->data;
        $data['id'] = $model->id;
        $data['role'] = $model->role->id;

        $this->postGraphQLBackOffice([
            'query' => $this->getQueryStrOnlyRequiredField($data)
        ])
            ->assertJson([
                'data' => [
                    self::MUTATION => [
                        'id' => $model->id,
                        'name' => $data['name'],
                        'email' => $data['email'],
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
                    id: "%s"
                    input: {
                        name: "%s"
                        email: "%s"
                        role: "%s"
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
                }
            }',
            self::MUTATION,
            data_get($data, 'id'),
            data_get($data, 'name'),
            data_get($data, 'email'),
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

        /** @var $model Admin */
        $model = $this->adminBuilder
            ->phone(verify: true)
            ->email(verify: true)
            ->lang($lang)
            ->create();

        $data = $this->data;
        $data['id'] = $model->id;
        $data['role'] = $role->id;
        $data['lang'] = $model->lang;
        $data['phone'] = $model->phone->phone->getValue();

        $this->postGraphQLBackOffice([
            'query' => $this->getQueryStr($data)
        ])
        ;

        $data[$field] = $value;

        $res = $this->postGraphQLBackOffice([
            'query' => $this->getQueryStr($data)
        ])
        ;

        if($field == 'id') {
            $this->assertResponseHasValidationMessage($res, $field, [__($msgKey, $attributes)]);
        } else {
            $this->assertResponseHasValidationMessage($res, 'input.'.$field, [__($msgKey, $attributes)]);
        }
    }

    public static function validate(): array
    {
        return [
            ['id', null, 'validation.required', ['attribute' => 'id']],
            ['id', 99999, 'validation.exists', ['attribute' => 'id']],
            ['name', null, 'validation.required', ['attribute' => 'name']],
            ['name', 't', 'validation.custom.name.name_rule', ['attribute' => 'Name']],
            ['email', null, 'validation.required', ['attribute' => 'email']],
            ['email', 'wrong-email', 'validation.email', ['attribute' => 'email']],
            ['email', 'test@test.com', 'validation.unique', ['attribute' => 'email']],
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
        /** @var $model Admin */
        $model = $this->adminBuilder->create();

        $data = $this->data;
        $data['id'] = $model->id;
        $data['role'] = $model->role->id;

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

        /** @var $model Admin */
        $model = $this->adminBuilder->create();

        $data = $this->data;
        $data['id'] = $model->id;
        $data['role'] = $model->role->id;

        $res = $this->postGraphQLBackOffice([
            'query' => $this->getQueryStrOnlyRequiredField($data)
        ])
        ;

        $this->assertPermission($res);
    }
}
