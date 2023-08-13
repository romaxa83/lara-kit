<?php

namespace Tests\Feature\Mutations\BackOffice\Permissions;

use App\GraphQL\Mutations\BackOffice\Permission\RoleUpdateMutation;
use App\Modules\Permissions\Enums\Guard;
use App\Modules\Permissions\Models\Role;
use App\Permissions\Roles\RoleUpdatePermission;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Builders\Admins\AdminBuilder;
use Tests\Builders\Localization\LanguageBuilder;
use Tests\Builders\Permissions\PermissionBuilder;
use Tests\Builders\Permissions\RoleBuilder;
use Tests\Builders\Users\UserBuilder;
use Tests\TestCase;
use Tests\Traits\Assert\AssertErrors;

class RoleUpdateMutationTest extends TestCase
{
    use DatabaseTransactions;
    use AssertErrors;

    protected RoleBuilder $roleBuilder;
    protected PermissionBuilder $permissionBuilder;
    protected AdminBuilder $adminBuilder;
    protected LanguageBuilder $langBuilder;
    protected UserBuilder $userBuilder;

    public const MUTATION = RoleUpdateMutation::NAME;

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
            'name' => 'admin role',
        ];

        $this->langInit();
    }

    /** @test */
    public function success_update()
    {
        $this->loginAsSuperAdmin();

        $perm_1 = $this->permissionBuilder->guard(Guard::ADMIN())->create();

        /** @var $model Role */
        $model = $this->roleBuilder
            ->permissions($perm_1)
            ->asAdmin()
            ->withTranslation()
            ->create();

        $perm_2 = $this->permissionBuilder->guard(Guard::ADMIN())->create();
        $perm_3 = $this->permissionBuilder->guard(Guard::ADMIN())->create();

        $data = $this->data;
        $data['id'] = $model->id;
        $data['name'] = $model->name;
        $data['translations'] = [
            [
                'title' => 'admin role en',
                'lang' => 'en',
            ]
        ];
        $data['permissions'] = [$perm_3->name, $perm_2->name];

        $this->assertNotEquals(
            $model->translations->where('lang', $data['translations'][0]['lang'])->first()->title,
            $data['translations'][0]['title']
        );
        $this->assertCount(1, $model->permissions);

        $this->postGraphQLBackOffice([
            'query' => $this->getQueryStr($data)
        ])
            ->assertJson([
                'data' => [
                    self::MUTATION => [
                        'id' => $model->id,
                        'name' => data_get($data, 'name'),
                        'guard' => $model->guard_name,
                        'translation' => [
                            'language' => $data['translations'][0]['lang'],
                            'title' => $data['translations'][0]['title'],
                        ]
                    ],
                ]
            ])
            ->assertJsonCount(2, 'data.'.self::MUTATION.'.permissions')
            ->assertJsonCount(1, 'data.'.self::MUTATION.'.translations')
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
                        permissions: ["%s", "%s"]
                        translations: [
                            {
                                lang: "%s",
                                title: "%s",
                            }
                        ]
                    },
                ) {
                    id
                    name
                    guard
                    permissions {
                        name
                    }
                    translations {
                        id
                    }
                    translation {
                        language
                        title
                    }
                }
            }',
            self::MUTATION,
            data_get($data, 'id'),
            data_get($data, 'name'),
            data_get($data, 'permissions.0'),
            data_get($data, 'permissions.1'),
            data_get($data, 'translations.0.lang'),
            data_get($data, 'translations.0.title'),
        );
    }

    /** @test */
    public function success_create_as_admin()
    {
        $perm = $this->permissionBuilder
            ->guard(Guard::ADMIN())
            ->name(RoleUpdatePermission::KEY)->create();
        $this->loginAsAdmin(null, $perm);

        $this->roleBuilder->name('manager')->asUser()->create();

        $perm_1 = $this->permissionBuilder->guard(Guard::ADMIN())->create();

        /** @var $model Role */
        $model = $this->roleBuilder
            ->permissions($perm_1)
            ->asAdmin()
            ->withTranslation()
            ->create();

        $perm_2 = $this->permissionBuilder->guard(Guard::ADMIN())->create();
        $perm_3 = $this->permissionBuilder->guard(Guard::ADMIN())->create();

        $data = $this->data;
        $data['id'] = $model->id;
        $data['name'] = 'manager';
        $data['translations'] = [
            [
                'title' => 'admin role en',
                'lang' => 'en',
            ]
        ];
        $data['permissions'] = [$perm_3->name, $perm_2->name];

        $this->assertNotEquals($model->name, $data['name']);
        $this->assertCount(1, $model->permissions);

        $this->postGraphQLBackOffice([
            'query' => $this->getQueryStr($data)
        ])
            ->assertJson([
                'data' => [
                    self::MUTATION => [
                        'id' => $model->id,
                        'name' => data_get($data, 'name'),
                    ],
                ]
            ])
            ->assertJsonCount(2, 'data.'.self::MUTATION.'.permissions')
        ;
    }

    /**
     * @dataProvider validate
     * @test
     */
    public function validate_data($field, $value, $msgKey, $attributes = [])
    {
        $this->loginAsSuperAdmin();

        $perm_1 = $this->permissionBuilder->guard(Guard::ADMIN())->create();

        /** @var $model Role */
        $model = $this->roleBuilder
            ->permissions($perm_1)
            ->asAdmin()
            ->withTranslation()
            ->create();

        $perm_2 = $this->permissionBuilder->guard(Guard::ADMIN())->create();
        $perm_3 = $this->permissionBuilder->guard(Guard::ADMIN())->create();

        $data = $this->data;
        $data['id'] = $model->id;
        $data['name'] = 'manager';
        $data['translations'] = [
            [
                'title' => 'admin role en',
                'lang' => 'en',
            ]
        ];
        $data['permissions'] = [$perm_3->name, $perm_2->name];

        $data[$field] = $value;

        $res = $this->postGraphQLBackOffice([
            'query' => $this->getQueryStr($data)
        ])
        ;

        if($field == 'id'){
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
            ['name', 't', 'validation.min.string', ['attribute' => 'name', 'min' => 3]],
            ['translations', '', 'validation.custom.translations.not_contain_supporting_lang'],
            ['translations', null, 'validation.custom.translations.not_contain_supporting_lang'],
        ];
    }

    /** @test */
    public function fail_not_support_languages()
    {
        $this->loginAsSuperAdmin();

        /** @var $model Role */
        $model = $this->roleBuilder
            ->asAdmin()
            ->withTranslation()
            ->create();

        $perm = $this->permissionBuilder->guard(Guard::ADMIN())->create();
        $data = $this->data;
        $data['id'] = $model->id;
        $data['translations'] = [
            [
                'title' => 'admin role en',
                'lang' => 'en',
            ],
            [
                'title' => 'admin role en',
                'lang' => 'ru',
            ]
        ];
        $data['permissions'] = [$perm->name];

        $res = $this->postGraphQLBackOffice([
            'query' => $this->getQueryStrTwoLangs($data)
        ])
        ;

        $this->assertResponseHasValidationMessage($res, 'input.translations',
            [__('validation.custom.translations.not_contain_supporting_lang')]
        );
    }

    /** @test */
    public function fail_translation_without_title()
    {
        $this->loginAsSuperAdmin();

        /** @var $model Role */
        $model = $this->roleBuilder
            ->asAdmin()
            ->withTranslation()
            ->create();

        $perm = $this->permissionBuilder->guard(Guard::ADMIN())->create();
        $data = $this->data;
        $data['id'] = $model->id;
        $data['translations'] = [
            [
                'title' => null,
                'lang' => 'en',
            ],
            [
                'title' => 'admin role en',
                'lang' => 'ru',
            ]
        ];
        $data['permissions'] = [$perm->name];

        $res = $this->postGraphQLBackOffice([
            'query' => $this->getQueryStrTwoLangs($data)
        ])
        ;

        $this->assertResponseHasValidationMessage($res, 'input.translations.0.title',
            [__('validation.required', ['attribute' => 'input.translations.0.title'])]
        );
    }

    /** @test */
    public function fail_wrong_perm()
    {
        $this->loginAsSuperAdmin();

        /** @var $model Role */
        $model = $this->roleBuilder
            ->asAdmin()
            ->withTranslation()
            ->create();

        $perm_2 = $this->permissionBuilder->guard(Guard::ADMIN())->create();
        $perm_3 = $this->permissionBuilder->guard(Guard::ADMIN())->create();

        $data = $this->data;
        $data['id'] = $model->id;
        $data['name'] = $model->name;
        $data['translations'] = [
            [
                'title' => 'admin role en',
                'lang' => 'en',
            ]
        ];
        $data['permissions'] = ['wrong-perm', $perm_2->name];

        $res = $this->postGraphQLBackOffice([
            'query' => $this->getQueryStr($data)
        ])
        ;

        $this->assertResponseHasValidationMessage($res, 'input.permissions',
            [__('validation.custom.permissions.contain_not_valid')]
        );
    }

    /** @test */
    public function fail_perm_another_guard()
    {
        $this->loginAsSuperAdmin();

        /** @var $model Role */
        $model = $this->roleBuilder
            ->asAdmin()
            ->withTranslation()
            ->create();

        $perm_2 = $this->permissionBuilder->guard(Guard::USER())->create();
        $perm_3 = $this->permissionBuilder->guard(Guard::ADMIN())->create();

        $data = $this->data;
        $data['id'] = $model->id;
        $data['name'] = $model->name;
        $data['translations'] = [
            [
                'title' => 'admin role en',
                'lang' => 'en',
            ]
        ];
        $data['permissions'] = [$perm_3->name, $perm_2->name];

        $res = $this->postGraphQLBackOffice([
            'query' => $this->getQueryStr($data)
        ])
        ;

        $this->assertResponseHasValidationMessage($res, 'input.permissions',
            [__('validation.custom.permissions.contain_not_valid')]
        );
    }

    /** @test */
    public function fail_not_uniq_role_name()
    {
        $this->loginAsSuperAdmin();

        /** @var $role Role */
        $role = $this->roleBuilder->name('manager')->create();

        /** @var $model Role */
        $model = $this->roleBuilder
            ->asAdmin()
            ->withTranslation()
            ->create();

        $perm_2 = $this->permissionBuilder->guard(Guard::ADMIN())->create();
        $perm_3 = $this->permissionBuilder->guard(Guard::ADMIN())->create();

        $data = $this->data;
        $data['id'] = $model->id;
        $data['name'] = $role->name;
        $data['translations'] = [
            [
                'title' => 'admin role en',
                'lang' => 'en',
            ]
        ];
        $data['permissions'] = [$perm_3->name, $perm_2->name];

        $res = $this->postGraphQLBackOffice([
            'query' => $this->getQueryStr($data)
        ])
        ;

        $res = $this->postGraphQLBackOffice([
            'query' => $this->getQueryStr($data)
        ])
        ;

        $this->assertResponseHasValidationMessage($res, 'input.name',
            [__('validation.custom.permissions.role_name_not_unique')]
        );
    }

    protected function getQueryStrTwoLangs(array $data): string
    {
        return sprintf(
            '
            mutation {
                %s (
                    id: %s
                    input: {
                        name: "%s"
                        permissions: ["%s"]
                        translations: [
                            {
                                lang: "%s",
                                title: "%s",
                            },
                            {
                                lang: "%s",
                                title: "%s",
                            }
                        ]
                    },
                ) {
                    id
                    name
                }
            }',
            self::MUTATION,
            data_get($data, 'id'),
            data_get($data, 'name'),
            data_get($data, 'permissions.0'),
            data_get($data, 'translations.0.lang'),
            data_get($data, 'translations.0.title'),
            data_get($data, 'translations.1.lang'),
            data_get($data, 'translations.1.title'),
        );
    }

    /** @test */
    public function not_auth()
    {
        /** @var $model Role */
        $model = $this->roleBuilder
            ->asAdmin()
            ->withTranslation()
            ->create();

        $perm_2 = $this->permissionBuilder->guard(Guard::ADMIN())->create();
        $perm_3 = $this->permissionBuilder->guard(Guard::ADMIN())->create();

        $data = $this->data;
        $data['id'] = $model->id;
        $data['translations'] = [
            [
                'title' => 'admin role en',
                'lang' => 'en',
            ]
        ];
        $data['permissions'] = [$perm_3->name, $perm_2->name];

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

        /** @var $model Role */
        $model = $this->roleBuilder
            ->asAdmin()
            ->withTranslation()
            ->create();

        $perm_2 = $this->permissionBuilder->guard(Guard::ADMIN())->create();
        $perm_3 = $this->permissionBuilder->guard(Guard::ADMIN())->create();

        $data = $this->data;
        $data['id'] = $model->id;
        $data['translations'] = [
            [
                'title' => 'admin role en',
                'lang' => 'en',
            ]
        ];
        $data['permissions'] = [$perm_3->name, $perm_2->name];

        $res = $this->postGraphQLBackOffice([
            'query' => $this->getQueryStr($data)
        ])
        ;

        $this->assertPermission($res);
    }
}

