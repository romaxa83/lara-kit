<?php

namespace Tests\Feature\Mutations\FrontOffice\Verification\Phone;

use App\GraphQL\Mutations\FrontOffice\Verification\Phone\RequestMutation;
use App\Modules\User\Models\User;
use App\Modules\Utils\Phones\Events\RequestVerifyEvent;
use App\Modules\Utils\Phones\Listeners\SendSmsListeners;
use App\Modules\Utils\Tokenizer\Tokenizer;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;
use Tests\Builders\Users\UserBuilder;
use Tests\TestCase;
use Tests\Traits\Assert\AssertErrors;

class RequestMutationTest extends TestCase
{
    use DatabaseTransactions;
    use AssertErrors;

    protected UserBuilder $userBuilder;

    public const MUTATION = RequestMutation::NAME;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userBuilder = resolve(UserBuilder::class);

        $this->langInit();
    }

    /** @test */
    public function success_request_as_admin()
    {
        Event::fake([RequestVerifyEvent::class]);

        /** @var $model User */
        $model = $this->userBuilder->phone()->create();

        $this->assertFalse($model->isPhoneVerified());
        $this->assertNUll($model->phone->code);
        $this->assertNUll($model->phone->code_expired_at);

        $res = $this->postGraphQL([
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
        /** @var $model User */
        $model = $this->userBuilder->phone(verify: true)->create();

        $this->assertTrue($model->isPhoneVerified());

        $this->postGraphQL([
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
        /** @var $model User */
        $model = $this->userBuilder->phone()->create();

        $res = $this->postGraphQL([
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

