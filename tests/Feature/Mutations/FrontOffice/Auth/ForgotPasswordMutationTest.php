<?php

namespace Tests\Feature\Mutations\FrontOffice\Auth;

use App\GraphQL\Mutations\FrontOffice\Auth\ForgotPasswordMutation;
use App\Modules\User\Models\User;
use App\Notifications\Auth\ForgotPasswordVerificationNotification;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Notification;
use Tests\Builders\Users\UserBuilder;
use Tests\TestCase;
use Tests\Traits\Assert\AssertNotification;

class ForgotPasswordMutationTest extends TestCase
{
    use DatabaseTransactions;
    use AssertNotification;

    public const MUTATION = ForgotPasswordMutation::NAME;

    private UserBuilder $userBuilder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->langInit();

        $this->userBuilder = resolve(UserBuilder::class);
    }

    /** @test */
    public function success_forgot_password(): void
    {
        Notification::fake();

        /** @var $model User */
        $model = $this->userBuilder->create();

        $this->postGraphQL([
            'query' => $this->getQueryStr($model->email->getValue())
        ])
            ->assertOk()
            ->assertJson([
                'data' => [
                    self::MUTATION => [
                        'success' => true,
                        'message' => __('messages.forgot_password.send.success', ['email' => $model->email->getValue()]),
                    ]
                ]
            ])
        ;

        $this->assertNotificationSentTo(
            $model->email->getValue(),
            ForgotPasswordVerificationNotification::class
        );
    }

    /** @test */
    public function fail_wrong_email(): void
    {
        Notification::fake();

        $email = 'test@test.com';

        $this->postGraphQL([
            'query' => $this->getQueryStr($email)
        ])
            ->assertJson([
                'data' => [
                    self::MUTATION => [
                        'success' => false,
                        'message' => __('exceptions.not_found_by_email', ['email' => $email]),
                    ]
                ]
            ])
        ;

        $this->assertNotificationNotSentTo(
            $email,
            ForgotPasswordVerificationNotification::class
        );
    }

    protected function getQueryStr(string $email): string
    {
        return sprintf(
            '
            mutation {
                %s (
                    email: "%s",
                ) {
                    success
                    message
                }
            }',
            self::MUTATION,
            $email,
        );
    }
}

