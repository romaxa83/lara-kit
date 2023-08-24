<?php

namespace Tests\Feature\Mutations\BackOffice\Verification\Phone;

use App\GraphQL\Mutations\BackOffice\Verification\Phone\VerificationMutation;
use App\Modules\Utils\Phones\Models\Phone;
use App\Modules\Utils\Tokenizer\Tokenizer;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Builders\Admins\AdminBuilder;
use Tests\Builders\Utils\PhoneBuilder;
use Tests\TestCase;
use Tests\Traits\Assert\AssertErrors;

class VerificationMutationTest extends TestCase
{
    use DatabaseTransactions;
    use AssertErrors;

    protected AdminBuilder $adminBuilder;
    protected PhoneBuilder $phoneBuilder;

    public const MUTATION = VerificationMutation::NAME;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminBuilder = resolve(AdminBuilder::class);
        $this->phoneBuilder = resolve(PhoneBuilder::class);

        $this->langInit();
    }

    /** @test */
    public function success_verify()
    {
        $admin = $this->adminBuilder->create();

        /** @var $model Phone */
        $model = $this->phoneBuilder
            ->code(1111, CarbonImmutable::now()->addMinute())
            ->model($admin)
            ->create();

        $token = Tokenizer::encryptToken([
            'model_id' => $model->id,
            'model_class' => $model::class,
            'code' => $model->code,
            'field_check_code' => 'code',
        ]);

        $this->assertFalse($model->isVerify());

        $this->postGraphQLBackOffice([
            'query' => $this->getQueryStr([
                'token' => $token,
                'code' => $model->code,
            ])
        ])
            ->assertJson([
                'data' => [
                    self::MUTATION => [
                        'success' => true,
                        'message' => __('messages.phone.phone_verify_success'),
                    ],
                ]
            ])
        ;

        $model->refresh();

        $this->assertTrue($model->isVerify());
    }

    /** @test */
    public function fail_wrong_code()
    {
        $admin = $this->adminBuilder->create();

        /** @var $model Phone */
        $model = $this->phoneBuilder
            ->code(1111, CarbonImmutable::now()->addMinute())
            ->model($admin)
            ->create();

        $token = Tokenizer::encryptToken([
            'model_id' => $model->id,
            'model_class' => $model::class,
            'code' => $model->code,
            'field_check_code' => 'code',
        ]);

        $this->assertFalse($model->isVerify());

        $this->postGraphQLBackOffice([
            'query' => $this->getQueryStr([
                'token' => $token,
                'code' => $model->code + 1,
            ])
        ])
            ->assertJson([
                'data' => [
                    self::MUTATION => [
                        'success' => false,
                        'message' => __('exceptions.phone.verify.code_is_not_correct'),
                    ],
                ]
            ])
        ;

        $model->refresh();

        $this->assertFalse($model->isVerify());
    }

    /** @test */
    public function fail_expired_code()
    {
        $admin = $this->adminBuilder->create();

        /** @var $model Phone */
        $model = $this->phoneBuilder
            ->code(1111, CarbonImmutable::now()->subMinute())
            ->model($admin)
            ->create();

        $token = Tokenizer::encryptToken([
            'model_id' => $model->id,
            'model_class' => $model::class,
            'code' => $model->code,
            'field_check_code' => 'code',
        ]);

        $this->assertFalse($model->isVerify());

        $this->postGraphQLBackOffice([
            'query' => $this->getQueryStr([
                'token' => $token,
                'code' => $model->code,
            ])
        ])
            ->assertJson([
                'data' => [
                    self::MUTATION => [
                        'success' => false,
                        'message' => __('exceptions.phone.verify.code_has_expired'),
                    ],
                ]
            ])
        ;

        $model->refresh();

        $this->assertFalse($model->isVerify());
    }

    /**
     * @dataProvider validate
     * @test
     */
    public function validate_data($field, $value, $msgKey, $attributes = [])
    {
        $admin = $this->adminBuilder->create();

        /** @var $model Phone */
        $model = $this->phoneBuilder
            ->code(1111, CarbonImmutable::now()->subMinute())
            ->model($admin)
            ->create();

        $data = [
            'token' => 'token',
            'code' => 'token'
        ];
        $data[$field] = $value;

        $res = $this->postGraphQLBackOffice([
            'query' => $this->getQueryStr($data)
        ]);

        $this->assertResponseHasValidationMessage($res, $field, [__($msgKey, $attributes)]);
    }

    public static function validate(): array
    {
        return [
            ['code', null, 'validation.required', ['attribute' => 'code']],
            ['token', null, 'validation.required', ['attribute' => 'token']],
        ];
    }

    protected function getQueryStr(array $data): string
    {
        return sprintf(
            '
            mutation {
                %s (
                    code: "%s"
                    token: "%s"
                ) {
                    message
                    success
                }
            }',
            self::MUTATION,
            $data['code'],
            $data['token'],
        );
    }
}
