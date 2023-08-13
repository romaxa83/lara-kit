<?php

namespace Tests\Feature\Mutations\BackOffice\Localization;

use App\Enums\CacheKeyEnum;
use App\GraphQL\Mutations\BackOffice\Localization\TranslationCreateOrUpdateMutation;
use App\Modules\Localization\Models\Translation;
use App\Modules\Permissions\Enums\Guard;
use App\Permissions\Localization\Translation\TranslationUpdatePermission;
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

class TranslationCreateOrUpdateMutationTest extends TestCase
{
    use DatabaseTransactions;
    use AssertErrors;
    use WithFaker;

    protected RoleBuilder $roleBuilder;
    protected PermissionBuilder $permissionBuilder;
    protected AdminBuilder $adminBuilder;
    protected LanguageBuilder $langBuilder;
    protected TranslationBuilder $translationBuilder;

    public const MUTATION = TranslationCreateOrUpdateMutation::NAME;

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
            'button.create' => [
                'place' => 'site',
                'key' => 'button.create',
                'text' => 'create',
                'lang' => 'en',
            ],
            'button.update' => [
                'place' => 'site',
                'key' => 'button.update',
                'text' => 'update',
                'lang' => 'en',
            ]
        ];

        $this->langInit();
    }

    /** @test */
    public function success_create()
    {
        Cache::tags(CacheKeyEnum::TRANSLATIONS)->rememberForever('key',fn() => 'value');

        $this->loginAsSuperAdmin();

        $data = $this->data;

        $this->assertEmpty(Translation::query()->get());

        $this->assertEquals(Cache::tags(CacheKeyEnum::TRANSLATIONS)->get('key'), 'value');

        $this->postGraphQLBackOffice([
            'query' => $this->getQueryStr($data)
        ])
            ->assertJson([
                'data' => [
                    self::MUTATION => [
                        'success' => true,
                        'message' => __('messages.localization.translation.actions.install.success'),
                    ],
                ]
            ])
        ;

        $translations = Translation::query()->get();

        foreach ($data as $key => $item){
            /** @var $t Translation */
            $t = $translations->where('key', $key)->first();
            $this->assertEquals($t->lang, $item['lang']);
            $this->assertEquals($t->place, $item['place']);
            $this->assertEquals($t->text, $item['text']);
        }

        $this->assertNull(Cache::tags(CacheKeyEnum::TRANSLATIONS)->get('key'));
    }

    /** @test */
    public function success_create_and_update()
    {
        $this->loginAsSuperAdmin();

        $data = $this->data;

        /** @var $t_1 Translation */
        $t_1 = $this->translationBuilder
            ->place($data['button.create']['place'])
            ->key($data['button.create']['key'])
            ->text($data['button.create']['text'])
            ->lang($data['button.create']['lang'])
            ->create();

        $data['button.create']['text'] = 'new text';

        $translations = Translation::query()->get();

        $this->assertCount(1, $translations);
        $this->assertNotEquals(
            $translations->where('key', 'button.create')->first()->text,
            $data['button.create']['text']
        );

        $this->postGraphQLBackOffice([
            'query' => $this->getQueryStr($data)
        ])
            ->assertJson([
                'data' => [
                    self::MUTATION => [
                        'success' => true,
                        'message' => __('messages.localization.translation.actions.install.success'),
                    ],
                ]
            ])
        ;

        $translations = Translation::query()->get();

        $this->assertCount(2, $translations);
        $this->assertEquals(
            $translations->where('key', 'button.create')->first()->text,
            $data['button.create']['text']
        );
    }

    /** @test */
    public function success_only_update()
    {
        $this->loginAsSuperAdmin();

        $data = $this->data;

        $this->translationBuilder
            ->place($data['button.create']['place'])
            ->key($data['button.create']['key'])
            ->text($data['button.create']['text'])
            ->lang($data['button.create']['lang'])
            ->create();
        $this->translationBuilder
            ->place($data['button.update']['place'])
            ->key($data['button.update']['key'])
            ->text($data['button.update']['text'])
            ->lang($data['button.update']['lang'])
            ->create();

        $data['button.create']['text'] = 'new text';

        $translations = Translation::query()->get();

        $this->assertCount(2, $translations);
        $this->assertNotEquals(
            $translations->where('key', 'button.create')->first()->text,
            $data['button.create']['text']
        );
        $this->assertEquals(
            $translations->where('key', 'button.update')->first()->text,
            $data['button.update']['text']
        );

        $tmp[] = $data['button.create'];

        $this->postGraphQLBackOffice([
            'query' => $this->getQueryStrOne($tmp)
        ])
            ->assertJson([
                'data' => [
                    self::MUTATION => [
                        'success' => true,
                        'message' => __('messages.localization.translation.actions.install.success'),
                    ],
                ]
            ])
        ;

        $translations = Translation::query()->get();

        $this->assertCount(2, $translations);
        $this->assertEquals(
            $translations->where('key', 'button.create')->first()->text,
            $data['button.create']['text']
        );
        $this->assertEquals(
            $translations->where('key', 'button.update')->first()->text,
            $data['button.update']['text']
        );
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
                            text: "%s"
                            lang: "%s"
                        },
                        {
                            place: "%s"
                            key: "%s"
                            text: "%s"
                            lang: "%s"
                        }
                    ],
                ) {
                    success
                    message
                }
            }',
            self::MUTATION,
            $data['button.create']['place'],
            $data['button.create']['key'],
            $data['button.create']['text'],
            $data['button.create']['lang'],
            $data['button.update']['place'],
            $data['button.update']['key'],
            $data['button.update']['text'],
            $data['button.update']['lang'],
        );
    }

    protected function getQueryStrOne(array $data): string
    {
        return sprintf(
            '
            mutation {
                %s (
                    input: [
                        {
                            place: "%s"
                            key: "%s"
                            text: "%s"
                            lang: "%s"
                        }
                    ],
                ) {
                    success
                    message
                }
            }',
            self::MUTATION,
            $data['0']['place'],
            $data['0']['key'],
            $data['0']['text'],
            $data['0']['lang']
        );
    }

    /** @test */
    public function success_create_as_admin()
    {
        $perm = $this->permissionBuilder
            ->guard(Guard::ADMIN())
            ->name(TranslationUpdatePermission::KEY)->create();
        $this->loginAsAdmin(null, $perm);

        $data = $this->data;

        $this->postGraphQLBackOffice([
            'query' => $this->getQueryStr($data)
        ])
            ->assertJson([
                'data' => [
                    self::MUTATION => [
                        'success' => true,
                        'message' => __('messages.localization.translation.actions.install.success'),
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

        $data[] = $this->data['button.create'];

        $data[0][$field] = $value;

        $res = $this->postGraphQLBackOffice([
            'query' => $this->getQueryStrOne($data)
        ])
        ;

        $this->assertResponseHasValidationMessage($res, 'input.0.'.$field, [__($msgKey, $attributes)]);
    }

    public static function validate(): array
    {
        return [
            ['place', null, 'validation.required', ['attribute' => 'input.0.place']],
            ['place', 'qq', 'validation.min.string', ['attribute' => 'input.0.place', 'min' => 3]],
            ['key', null, 'validation.required', ['attribute' => 'input.0.key']],
            ['key', 'qq', 'validation.min.string', ['attribute' => 'input.0.key', 'min' => 3]],
            ['text', null, 'validation.required', ['attribute' => 'input.0.text']],
            ['text', 'q', 'validation.min.string', ['attribute' => 'input.0.text', 'min' => 2]],
            ['lang', null, 'validation.required', ['attribute' => 'input.0.lang']],
            ['lang', 'q', 'validation.min.string', ['attribute' => 'input.0.lang', 'min' => 2]],
            ['lang', 'qqqqq', 'validation.max.string', ['attribute' => 'input.0.lang', 'max' => 3]],
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
