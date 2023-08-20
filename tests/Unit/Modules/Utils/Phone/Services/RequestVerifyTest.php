<?php

namespace Tests\Unit\Modules\Utils\Phone\Services;

use App\Modules\User\Models\User;
use App\Modules\Utils\Phones\Events\RequestVerifyEvent;
use App\Modules\Utils\Phones\Exceptions\PhoneVerificationException;
use App\Modules\Utils\Phones\Listeners\SendSmsListeners;
use App\Modules\Utils\Phones\Services\VerificationService;
use App\Modules\Utils\Tokenizer\Tokenizer;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;
use Tests\Builders\Users\UserBuilder;
use Tests\TestCase;

class RequestVerifyTest extends TestCase
{
    use DatabaseTransactions;

    protected UserBuilder $userBuilder;
    protected VerificationService $service;
    protected function setUp(): void
    {
        parent::setUp();

        $this->userBuilder = resolve(UserBuilder::class);
        $this->service = resolve(VerificationService::class);

        $this->langInit();
    }

    /** @test */
    public function success_request()
    {
        Event::fake([RequestVerifyEvent::class]);

        /** @var $user User */
        $user = $this->userBuilder->phone()->create();

        $this->assertFalse($user->phone->isVerify());
        $this->assertNull($user->phone->code);
        $this->assertNull($user->phone->code_expired_at);

        $date = CarbonImmutable::now();
        CarbonImmutable::setTestNow($date);

        $res = $this->service->requestVerify($user->phone);

        $user->refresh();

        $this->assertFalse($user->phone->isVerify());
        $this->assertNotNull($user->phone->code);
        $this->assertEquals(
            $user->phone->code_expired_at->timestamp,
            $date->addSeconds(config('sms.verify.sms_token_expired'))->timestamp
        );

        $tokenData = Tokenizer::decryptToken($res);
        $this->assertEquals($tokenData->modelId, $user->phone->id);
        $this->assertEquals($tokenData->modelClass, $user->phone::class);
        $this->assertEquals($tokenData->code, $user->phone->code);

        Event::assertDispatched(fn (RequestVerifyEvent $event) =>
            $event->getModel()->id === $user->phone->id
            && $event->getPhone()->getValue() === $user->phone->phone->getValue()
            && $event->getMsg() === __('messages.phone.sms.verify_code', ['code' => $user->phone->code])
        );
        Event::assertListening(RequestVerifyEvent::class, SendSmsListeners::class);
    }

    /** @test */
    public function fail_phone_already_verified()
    {
        /** @var $user User */
        $user = $this->userBuilder->phone(verify: true)->create();

        $this->assertTrue($user->phone->isVerify());


        $this->expectException(PhoneVerificationException::class);
        $this->expectExceptionMessage( __('exceptions.phone.verify.phone_already_verified'));

        $this->service->requestVerify($user->phone);
    }
}
