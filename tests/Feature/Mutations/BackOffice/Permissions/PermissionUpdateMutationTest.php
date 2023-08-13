<?php

namespace Tests\Feature\Mutations\BackOffice\Permissions;

use App\GraphQL\Mutations\BackOffice\Permission\PermissionUpdateMutation;
use App\Modules\Permissions\Enums\Guard;
use App\Modules\Permissions\Models\Permission;
use App\Modules\Permissions\Models\Role;
use App\Permissions\Roles\RoleUpdatePermission;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Builders\Admins\AdminBuilder;
use Tests\Builders\Localization\LanguageBuilder;
use Tests\Builders\Permissions\PermissionBuilder;
use Tests\TestCase;
use Tests\Traits\Assert\AssertErrors;

class PermissionUpdateMutationTest extends TestCase
{
    use DatabaseTransactions;
    use AssertErrors;

    protected PermissionBuilder $permissionBuilder;
    protected AdminBuilder $adminBuilder;
    protected LanguageBuilder $langBuilder;

    public const MUTATION = PermissionUpdateMutation::NAME;
    protected function setUp(): void
    {
        parent::setUp();

        $this->adminBuilder = resolve(AdminBuilder::class);
        $this->langBuilder = resolve(LanguageBuilder::class);
        $this->permissionBuilder = resolve(PermissionBuilder::class);

        $this->langInit();
    }

    /** @test */
    public function success_update()
    {
        $this->loginAsSuperAdmin();

        /** @var $model Permission */
        $model = $this->permissionBuilder
            ->withTranslation()
            ->create();

        $data['id'] = $model->id;
        $data['translations'] = [
            [
                'title' => 'admin create permission en',
                'lang' => 'en',
            ]
        ];

        $this->assertNotEquals(
            $model->translations->where('lang', $data['translations'][0]['lang'])->first()->title,
            $data['translations'][0]['title']
        );

        $this->postGraphQLBackOffice([
            'query' => $this->getQueryStr($data)
        ])
            ->assertJson([
                'data' => [
                    self::MUTATION => [
                        'id' => $model->id,
                        'translation' => [
                            'language' => $data['translations'][0]['lang'],
                            'title' => $data['translations'][0]['title'],
                        ]
                    ],
                ]
            ])
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
                        translations: [
                            {
                                lang: "%s",
                                title: "%s",
                            }
                        ]
                    },
                ) {
                    id
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

        /** @var $model Permission */
        $model = $this->permissionBuilder
            ->withTranslation()
            ->create();

        $data['id'] = $model->id;
        $data['translations'] = [
            [
                'title' => 'admin create permission en',
                'lang' => 'en',
            ]
        ];

        $this->assertNotEquals(
            $model->translations->where('lang', $data['translations'][0]['lang'])->first()->title,
            $data['translations'][0]['title']
        );

        $this->postGraphQLBackOffice([
            'query' => $this->getQueryStr($data)
        ])
            ->assertJson([
                'data' => [
                    self::MUTATION => [
                        'id' => $model->id,
                        'translation' => [
                            'language' => $data['translations'][0]['lang'],
                            'title' => $data['translations'][0]['title'],
                        ]
                    ],
                ]
            ])
            ->assertJsonCount(1, 'data.'.self::MUTATION.'.translations')
        ;
    }


    /** @test */
    public function fail_not_support_languages()
    {
        $this->loginAsSuperAdmin();

        /** @var $model Permission */
        $model = $this->permissionBuilder
            ->withTranslation()
            ->create();

        $data['id'] = $model->id;
        $data['translations'] = [
            [
                'title' => 'admin create permission en',
                'lang' => 'en',
            ],
            [
                'title' => 'admin create permission ru',
                'lang' => 'ru',
            ]
        ];

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

        /** @var $model Permission */
        $model = $this->permissionBuilder
            ->withTranslation()
            ->create();

        $data['id'] = $model->id;
        $data['translations'] = [
            [
                'title' => null,
                'lang' => 'en',
            ]
        ];

        $res = $this->postGraphQLBackOffice([
            'query' => $this->getQueryStr($data)
        ])
        ;

        $this->assertResponseHasValidationMessage($res, 'input.translations.0.title',
            [__('validation.required', ['attribute' => 'input.translations.0.title'])]
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
            data_get($data, 'translations.0.lang'),
            data_get($data, 'translations.0.title'),
            data_get($data, 'translations.1.lang'),
            data_get($data, 'translations.1.title'),
        );
    }

    /** @test */
    public function not_auth()
    {
        /** @var $model Permission */
        $model = $this->permissionBuilder
            ->withTranslation()
            ->create();

        $data['id'] = $model->id;
        $data['translations'] = [
            [
                'title' => 'admin create permission en',
                'lang' => 'en',
            ]
        ];

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

        /** @var $model Permission */
        $model = $this->permissionBuilder
            ->withTranslation()
            ->create();

        $data['id'] = $model->id;
        $data['translations'] = [
            [
                'title' => 'admin create permission en',
                'lang' => 'en',
            ]
        ];

        $res = $this->postGraphQLBackOffice([
            'query' => $this->getQueryStr($data)
        ])
        ;

        $this->assertPermission($res);
    }
}
