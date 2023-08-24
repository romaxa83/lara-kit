<?php

namespace Tests\Feature\Mutations\BackOffice\Verification\Phone;

use App\GraphQL\Mutations\BackOffice\Verification\Phone\RequestMutation;
use App\Modules\Admin\Models\Admin;
use App\Modules\Utils\Phones\Events\RequestVerifyEvent;
use App\Modules\Utils\Phones\Listeners\SendSmsListeners;
use App\Modules\Utils\Tokenizer\Tokenizer;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;
use Tests\Builders\Admins\AdminBuilder;
use Tests\TestCase;
use Tests\Traits\Assert\AssertErrors;

class RequestMutationTest extends TestCase
{
    use DatabaseTransactions;
    use AssertErrors;

    protected AdminBuilder $adminBuilder;

    public const MUTATION = RequestMutation::NAME;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminBuilder = resolve(AdminBuilder::class);

        $this->langInit();
    }

    /** @test */
    public function success_request_as_admin()
    {
        Event::fake([RequestVerifyEvent::class]);

        /** @var $model Admin */
        $model = $this->adminBuilder->phone()->create();

        $this->assertFalse($model->isPhoneVerified());
        $this->assertNUll($model->phone->code);
        $this->assertNUll($model->phone->code_expired_at);

        $res = $this->postGraphQLBackOffice([
            'query' => $this->getQueryStr($model->phone->phone->asString())
        ])
            ->assertJson([
                'data' => [
                    self::MUTATION => [
                        'success' => true
                    ],
                ]
            ])
        ;

        $tokenData = Tokenizer::decryptToken($res->json('data.'.self::MUTATION.'.message'));
        $this->assertEquals($tokenData->modelId, $model->phone->id);

        $model->refresh();

        $this->assertFalse($model->isPhoneVerified());
        $this->assertNotNUll($model->phone->code);
        $this->assertNotNUll($model->phone->code_expired_at);

        Event::assertListening(RequestVerifyEvent::class, SendSmsListeners::class);
    }

    /** @test */
    public function fail_phone_already_verification()
    {
        /** @var $model Admin */
        $model = $this->adminBuilder->phone(verify: true)->create();

        $this->assertTrue($model->isPhoneVerified());

        $this->postGraphQLBackOffice([
            'query' => $this->getQueryStr($model->phone->phone->asString())
        ])
            ->assertJson([
                'data' => [
                    self::MUTATION => [
                        'message' => __('exceptions.phone.verify.phone_already_verified'),
                        'success' => false
                    ],
                ]
            ])
        ;
    }

    /**
     * @dataProvider validate
     * @test
     */
    public function validate_data($value, $msgKey, $attributes = [])
    {
        /** @var $model Admin */
        $model = $this->adminBuilder->phone()->create();

        $res = $this->postGraphQLBackOffice([
            'query' => $this->getQueryStr($value)
        ]);

        $this->assertResponseHasValidationMessage($res, 'phone', [__($msgKey, $attributes)]);
    }

    public static function validate(): array
    {
        return [
            [null, 'validation.required', ['attribute' => 'phone']],
            ['r', 'validation.custom.phone.not_valid', ['value' => 'r']],
        ];
    }

    protected function getQueryStr($value): string
    {
        return sprintf(
            '
            mutation {
                %s (
                    phone: "%s"
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

