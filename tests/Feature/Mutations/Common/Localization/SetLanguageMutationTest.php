<?php

namespace Tests\Feature\Mutations\Common\Localization;

use App\GraphQL\Mutations\Common\Localization\SetLanguageMutation;
use App\Modules\Admin\Models\Admin;
use App\Modules\User\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Builders\Admins\AdminBuilder;
use Tests\Builders\Localization\LanguageBuilder;
use Tests\Builders\Users\UserBuilder;
use Tests\TestCase;
use Tests\Traits\Assert\AssertErrors;

class SetLanguageMutationTest extends TestCase
{
    use DatabaseTransactions;
    use AssertErrors;

    protected AdminBuilder $adminBuilder;
    protected UserBuilder $userBuilder;
    protected LanguageBuilder $langBuilder;

    public const MUTATION = SetLanguageMutation::NAME;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminBuilder = resolve(AdminBuilder::class);
        $this->userBuilder = resolve(UserBuilder::class);
        $this->langBuilder = resolve(LanguageBuilder::class);

        $this->langInit();
    }

    /** @test */
    public function success_set_as_admin()
    {
        /** @var $model Admin */
        $model = $this->loginAsAdmin();

        $lang = $this->langBuilder->slug('fr')->create();

        $this->assertNotEquals($model->lang, $lang->slug);

        $this->postGraphQLBackOffice([
            'query' => $this->getQueryStr($lang->slug)
        ])
            ->assertJson([
                'data' => [
                    self::MUTATION => [
                        'message' => __('messages.localization.success_set_lang'),
                        'success' => true
                    ],
                ]
            ])
        ;

        $model->refresh();
        $this->assertEquals($model->lang, $lang->slug);
    }

    /** @test */
    public function success_set_as_user()
    {
        /** @var $model User */
        $model = $this->loginAsUser();

        $lang = $this->langBuilder->slug('fr')->create();

        $this->assertNotEquals($model->lang, $lang->slug);

        $this->postGraphQL([
            'query' => $this->getQueryStr($lang->slug)
        ])
            ->assertJson([
                'data' => [
                    self::MUTATION => [
                        'message' => __('messages.localization.success_set_lang'),
                        'success' => true
                    ],
                ]
            ])
        ;

        $model->refresh();
        $this->assertEquals($model->lang, $lang->slug);
    }

    /**
     * @dataProvider validate
     * @test
     */
    public function validate_data($value, $msgKey, $attributes = [])
    {
        /** @var $model User */
        $this->loginAsUser();

        $this->langBuilder->slug('fr')->create();

        $res = $this->postGraphQLBackOffice([
            'query' => $this->getQueryStr($value)
        ])
        ;

        $this->assertResponseHasValidationMessage($res, 'lang', [__($msgKey, $attributes)]);
    }

    public static function validate(): array
    {
        return [
            [null, 'validation.required', ['attribute' => 'lang']],
            ['r', 'validation.min.string', ['attribute' => 'lang', 'min' => 2]],
            ['rrrrr', 'validation.max.string', ['attribute' => 'lang', 'max' => 3]],
            ['ru', 'validation.custom.lang.exist-languages', ['attribute' => 'lang']],
        ];
    }

    /** @test */
    public function not_auth()
    {
        $lang = $this->langBuilder->slug('fr')->create();

        $res = $this->postGraphQL([
            'query' => $this->getQueryStr($lang->slug)
        ])
        ;

        $this->assertUnauthorized($res);
    }

    protected function getQueryStr($value): string
    {
        return sprintf(
            '
            mutation {
                %s (
                    lang: "%s"
                ) {
                    message
                    success
                }
            }',
            self::MUTATION,
            $value,
        );
    }
}
