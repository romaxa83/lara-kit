<?php

namespace Tests\Feature\Mutations\BackOffice\Localization;

use App\Enums\CacheKeyEnum;
use App\GraphQL\Mutations\BackOffice\Localization\TranslationDeleteMutation;
use App\Modules\Localization\Models\Translation;
use App\Modules\Permissions\Enums\Guard;
use App\Permissions\Localization\Translation\TranslationDeletePermission;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Cache;
use Tests\Builders\Admins\AdminBuilder;
use Tests\Builders\Localization\LanguageBuilder;
use Tests\Builders\Localization\TranslationBuilder;
use Tests\Builders\Permissions\PermissionBuilder;
use Tests\Builders\Permissions\RoleBuilder;
use Tests\TestCase;
use Tests\Traits\Assert\AssertErrors;

class TranslationDeleteMutationTest extends TestCase
{
    use DatabaseTransactions;
    use AssertErrors;
    use WithFaker;

    protected RoleBuilder $roleBuilder;
    protected PermissionBuilder $permissionBuilder;
    protected AdminBuilder $adminBuilder;
    protected LanguageBuilder $langBuilder;
    protected TranslationBuilder $translationBuilder;

    public const MUTATION = TranslationDeleteMutation::NAME;

    protected array $data = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->roleBuilder = resolve(RoleBuilder::class);
        $this->adminBuilder = resolve(AdminBuilder::class);
        $this->langBuilder = resolve(LanguageBuilder::class);
        $this->permissionBuilder = resolve(PermissionBuilder::class);
        $this->translationBuilder = resolve(TranslationBuilder::class);

        $this->data = [
            [
                'place' => 'site',
                'key' => 'button.create',
                'text' => 'create',
                'lang' => 'en',
            ],
            [
                'place' => 'site',
                'key' => 'button.update',
                'text' => 'update',
                'lang' => 'en',
            ],
            [
                'place' => 'site',
                'key' => 'button.delete',
                'text' => 'update',
                'lang' => 'en',
            ]
        ];

        $this->langInit();
    }

    /** @test */
    public function success_delete()
    {
        Cache::tags(CacheKeyEnum::TRANSLATIONS)->rememberForever('key',fn() => 'value');

        $this->loginAsSuperAdmin();

        $data = $this->data;

        $t_1 = $this->translationBuilder
            ->place($data[0]['place'])
            ->key($data[0]['key'])
            ->lang($data[0]['lang'])
            ->create();
        $t_1_id = $t_1->id;
        $t_2 = $this->translationBuilder
            ->place($data[1]['place'])
            ->key($data[1]['key'])
            ->lang($data[1]['lang'])
            ->create();
        $t_2_id = $t_2->id;
        $this->translationBuilder
            ->place($data[2]['place'])
            ->key($data[2]['key'])
            ->lang($data[2]['lang'])
            ->create();

        $this->assertEquals(3, Translation::query()->count());

        $this->assertEquals(Cache::tags(CacheKeyEnum::TRANSLATIONS)->get('key'), 'value');

        $this->postGraphQLBackOffice([
            'query' => $this->getQueryStr($data)
        ])
            ->assertJson([
                'data' => [
                    self::MUTATION => [
                        'success' => true,
                        'message' => __('messages.localization.translation.actions.delete.success'),
                    ],
                ]
            ])
        ;

        $this->assertEquals(1, Translation::query()->count());
        $this->assertFalse(Translation::query()->where('id', $t_1_id)->exists());
        $this->assertFalse(Translation::query()->where('id', $t_2_id)->exists());

        $this->assertNull(Cache::tags(CacheKeyEnum::TRANSLATIONS)->get('key'));
    }

    /** @test */
    public function fail_delete_duplicate_data()
    {
        $this->loginAsSuperAdmin();

        $data = $this->data;

        $t_1 = $this->translationBuilder
            ->place($data[0]['place'])
            ->key($data[0]['key'])
            ->lang($data[0]['lang'])
            ->create();
        $t_1_id = $t_1->id;
        $t_2 = $this->translationBuilder
            ->place($data[1]['place'])
            ->key($data[1]['key'])
            ->lang($data[1]['lang'])
            ->create();
        $t_2_id = $t_2->id;
        $this->translationBuilder
            ->place($data[2]['place'])
            ->key($data[2]['key'])
            ->lang($data[2]['lang'])
            ->create();

        $this->assertEquals(3, Translation::query()->count());

        $data[1] = $data[0];

        $this->postGraphQLBackOffice([
            'query' => $this->getQueryStr($data)
        ])
            ->assertJson([
                'data' => [
                    self::MUTATION => [
                        'success' => false,
                        'message' => __('messages.localization.translation.actions.delete.fail'),
                    ],
                ]
            ])
        ;

        $this->assertEquals(3, Translation::query()->count());
    }

    protected function getQueryStr(array $data): string
    {
        return sprintf(
            '
            mutation {
                %s (
                    input: [
                        {
                            place: "%s"
                            key: "%s"
                            lang: "%s"
                        },
                        {
                            place: "%s"
                            key: "%s"
                            lang: "%s"
                        }
                    ],
                ) {
                    success
                    message
                }
            }',
            self::MUTATION,
            $data[0]['place'],
            $data[0]['key'],
            $data[0]['lang'],
            $data[1]['place'],
            $data[1]['key'],
            $data[1]['lang'],
        );
    }

    /** @test */
    public function success_create_as_admin()
    {
        $perm = $this->permissionBuilder
            ->guard(Guard::ADMIN())
            ->name(TranslationDeletePermission::KEY)->create();
        $this->loginAsAdmin(null, $perm);

        $data = $this->data;

        $this->translationBuilder
            ->place($data[0]['place'])
            ->key($data[0]['key'])
            ->lang($data[0]['lang'])
            ->create();
        $this->translationBuilder
            ->place($data[1]['place'])
            ->key($data[1]['key'])
            ->lang($data[1]['lang'])
            ->create();
        $this->translationBuilder
            ->place($data[2]['place'])
            ->key($data[2]['key'])
            ->lang($data[2]['lang'])
            ->create();

        $this->postGraphQLBackOffice([
            'query' => $this->getQueryStr($data)
        ])
            ->assertJson([
                'data' => [
                    self::MUTATION => [
                        'success' => true,
                        'message' => __('messages.localization.translation.actions.delete.success'),
                    ],
                ]
            ]);
    }

    /**
     * @dataProvider validate
     * @test
     */
    public function validate_data($field, $value, $msgKey, $attributes = [])
    {
        $this->loginAsSuperAdmin();

        $data = $this->data;

        $this->translationBuilder
            ->place($data[0]['place'])
            ->key($data[0]['key'])
            ->lang($data[0]['lang'])
            ->create();
        $this->translationBuilder
            ->place($data[1]['place'])
            ->key($data[1]['key'])
            ->lang($data[1]['lang'])
            ->create();
        $this->translationBuilder
            ->place($data[2]['place'])
            ->key($data[2]['key'])
            ->lang($data[2]['lang'])
            ->create();

        $data[0][$field] = $value;

        $res = $this->postGraphQLBackOffice([
            'query' => $this->getQueryStr($data)
        ])
        ;

        $this->assertResponseHasValidationMessage($res, 'input.0.'.$field, [__($msgKey, $attributes)]);
    }

    public static function validate(): array
    {
        return [
            ['place', null, 'validation.required', ['attribute' => 'input.0.place']],
            ['key', null, 'validation.required', ['attribute' => 'input.0.key']],
            ['lang', null, 'validation.required', ['attribute' => 'input.0.lang']],
            ['lang', 'ru', 'validation.custom.lang.exist-languages', ['attribute' => 'input.0.lang']],
        ];
    }

    /** @test */
    public function not_auth()
    {
        $res = $this->postGraphQLBackOffice([
            'query' => $this->getQueryStr($this->data)
        ])
        ;

        $this->assertUnauthorized($res);
    }

    /** @test */
    public function not_perm()
    {
        $this->loginAsAdmin();

        $res = $this->postGraphQLBackOffice([
            'query' => $this->getQueryStr($this->data)
        ])
        ;

        $this->assertPermission($res);
    }
}
