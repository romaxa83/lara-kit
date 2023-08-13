<?php

namespace Tests\Feature\Mutations\BackOffice\Auth;

use App\GraphQL\Mutations\BackOffice\Auth\ForgotPasswordMutation;
use App\Modules\Admin\Models\Admin;
use App\Notifications\Auth\ForgotPasswordVerificationNotification;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Notification;
use Tests\Builders\Admins\AdminBuilder;
use Tests\TestCase;
use Tests\Traits\Assert\AssertNotification;

class ForgotPasswordMutationTest extends TestCase
{
    use DatabaseTransactions;
    use AssertNotification;

    public const MUTATION = ForgotPasswordMutation::NAME;

    private AdminBuilder $adminBuilder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->langInit();

        $this->adminBuilder = resolve(AdminBuilder::class);
    }

    /** @test */
    public function success_forgot_password(): void
    {
        Notification::fake();

        /** @var $model Admin */
        $model = $this->adminBuilder->create();

        $this->postGraphQLBackOffice([
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

        $this->postGraphQLBackOffice([
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
