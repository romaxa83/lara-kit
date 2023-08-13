<?php

namespace Tests\Feature\Mutations\BackOffice\Auth;

use App\GraphQL\Mutations\BackOffice\Auth\ResetPasswordMutation;
use App\Modules\Admin\Models\Admin;
use App\Modules\Auth\Services\VerificationService;
use App\Notifications\Auth\ResetPasswordVerificationNotification;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\Builders\Admins\AdminBuilder;
use Tests\TestCase;
use Tests\Traits\Assert\AssertNotification;

class ResetPasswordMutationTest extends TestCase
{
    use DatabaseTransactions;
    use AssertNotification;

    public const MUTATION = ResetPasswordMutation::NAME;

    private AdminBuilder $adminBuilder;
    protected VerificationService $verificationService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->langInit();

        $this->adminBuilder = resolve(AdminBuilder::class);
        $this->verificationService = resolve(VerificationService::class);
    }

    /** @test */
    public function success_reset_password(): void
    {
        Notification::fake();

        /** @var $model Admin */
        $model = $this->adminBuilder->create();

        $password = 'Password1234';

        $this->assertFalse(Hash::check($password, $model->password));

        $this->postGraphQLBackOffice([
            'query' => $this->getQueryStr([
                'token' => $this->verificationService->encryptEmailToken($model),
                'password' => $password,
                'password_confirmation' => $password,
            ])
        ])
            ->assertOk()
            ->assertJson([
                'data' => [
                    self::MUTATION => [
                        'message' => __('messages.reset_password.action.success'),
                        'success' => true
                    ]
                ]
            ])
        ;

        $this->assertNotificationSentTo(
            $model->email->getValue(),
            ResetPasswordVerificationNotification::class
        );

        $model->refresh();

        $this->assertTrue(Hash::check($password, $model->password));
    }

    /** @test */
    public function fail_wrong_token(): void
    {
        Notification::fake();

        /** @var $model Admin */
        $model = $this->adminBuilder->create();

        $password = 'Password1234';

        $this->postGraphQLBackOffice([
            'query' => $this->getQueryStr([
                'token' => 'wrong_token',
                'password' => $password,
                'password_confirmation' => $password,
            ])
        ])
            ->assertJson([
                'data' => [
                    self::MUTATION => [
                        'message' => 'The payload is invalid.',
                        'success' => false
                    ]
                ]
            ])
        ;

        $this->assertNotificationNotSentTo(
            $model->email->getValue(),
            ResetPasswordVerificationNotification::class
        );
    }

    protected function getQueryStr(array $data): string
    {
        return sprintf(
            '
            mutation {
                %s (
                    input: {
                        token: "%s",
                        password: "%s",
                        password_confirmation: "%s",
                    }
                ) {
                    message
                    success
                }
            }',
            self::MUTATION,
            data_get($data, 'token'),
            data_get($data, 'password'),
            data_get($data, 'password_confirmation'),
        );
    }
}
