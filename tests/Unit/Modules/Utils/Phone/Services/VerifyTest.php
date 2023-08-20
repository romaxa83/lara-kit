<?php

namespace Tests\Unit\Modules\Utils\Phone\Services;

use App\Modules\User\Models\User;
use App\Modules\Utils\Phones\Exceptions\PhoneVerificationException;
use App\Modules\Utils\Phones\Models\Phone;
use App\Modules\Utils\Phones\Services\VerificationService;
use App\Modules\Utils\Tokenizer\Tokenizer;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Builders\Users\UserBuilder;
use Tests\Builders\Utils\PhoneBuilder;
use Tests\TestCase;

class VerifyTest extends TestCase
{
    use DatabaseTransactions;

    protected UserBuilder $userBuilder;
    protected PhoneBuilder $phoneBuilder;
    protected VerificationService $service;
    protected function setUp(): void
    {
        parent::setUp();

        $this->userBuilder = resolve(UserBuilder::class);
        $this->phoneBuilder = resolve(PhoneBuilder::class);
        $this->service = resolve(VerificationService::class);

        $this->langInit();
    }

    /** @test */
    public function success_verify()
    {
        /** @var $user User */
        $user = $this->userBuilder->create();
        $user->refresh();

        $date = CarbonImmutable::now();
        CarbonImmutable::setTestNow($date);

        $code = 9999;
        /** @var $model Phone */
        $model = $this->phoneBuilder->model($user)->code(
            $code, $date->addMinute()
        )->create();

        $this->assertFalse($user->phone->isVerify());
        $this->assertNotNull($user->phone->code);
        $this->assertNotNull($user->phone->code_expired_at);

        $token = Tokenizer::encryptToken([
            'model_id' => $model->id,
            'model_class' => $model::class,
            'code' => $model->code,
            'field_check_code' => 'code',
        ]);

        $this->assertTrue($this->service->verify($token, $code));

        $user->refresh();

        $this->assertTrue($user->phone->isVerify());
        $this->assertNull($user->phone->code);
        $this->assertNull($user->phone->code_expired_at);
    }

    /** @test */
    public function fail_code_has_expired()
    {
        /** @var $user User */
        $user = $this->userBuilder->create();
        $user->refresh();

        $date = CarbonImmutable::now();
        CarbonImmutable::setTestNow($date);

        $code = 9999;
        /** @var $model Phone */
        $model = $this->phoneBuilder->model($user)->code(
            $code, $date->subMinutes(10)
        )->create();

        $this->assertFalse($user->phone->isVerify());
        $this->assertNotNull($user->phone->code);
        $this->assertNotNull($user->phone->code_expired_at);

        $token = Tokenizer::encryptToken([
            'model_id' => $model->id,
            'model_class' => $model::class,
            'code' => $model->code,
            'field_check_code' => 'code',
        ]);

        $this->expectException(PhoneVerificationException::class);
        $this->expectExceptionMessage(__('exceptions.phone.verify.code_has_expired'));

        $this->service->verify($token, $code);
    }

    /** @test */
    public function fail_code_not_equals()
    {
        /** @var $user User */
        $user = $this->userBuilder->create();
        $user->refresh();

        $date = CarbonImmutable::now();
        CarbonImmutable::setTestNow($date);

        $code = 9999;
        /** @var $model Phone */
        $model = $this->phoneBuilder->model($user)->code(
            $code, $date->addMinute()
        )->create();

        $this->assertFalse($user->phone->isVerify());
        $this->assertNotNull($user->phone->code);
        $this->assertNotNull($user->phone->code_expired_at);

        $token = Tokenizer::encryptToken([
            'model_id' => $model->id,
            'model_class' => $model::class,
            'code' => $model->code,
            'field_check_code' => 'code',
        ]);

        $this->expectException(PhoneVerificationException::class);
        $this->expectExceptionMessage(__('exceptions.phone.verify.code_is_not_correct'));

        $this->service->verify($token, 1111);
    }

    /** @test */
    public function fail_not_found_model()
    {
        /** @var $user User */
        $user = $this->userBuilder->create();
        $user->refresh();

        $code = 9999;
        /** @var $model Phone */
        $model = $this->phoneBuilder->model($user)->code(
            $code, CarbonImmutable::now()
        )->create();

        $token = Tokenizer::encryptToken([
            'model_id' => $model->id + 1,
            'model_class' => $model::class,
            'code' => $model->code,
            'field_check_code' => 'code',
        ]);

        $this->expectException(PhoneVerificationException::class);
        $this->expectExceptionMessage(__('exceptions.phone.not_found_phone'));

        $this->service->verify($token, $code);
    }
}

