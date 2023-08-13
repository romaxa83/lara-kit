<?php

namespace Tests\Unit\Notifications\Auth;

use App\Modules\Admin\Models\Admin;
use App\Modules\User\Models\User;
use App\Notifications\Auth\ForgotPasswordVerificationNotification;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\HtmlString;
use Tests\Builders\Admins\AdminBuilder;
use Tests\Builders\Users\UserBuilder;
use Tests\TestCase;

class ForgotPasswordVerificationNotificationTest extends TestCase
{
    use DatabaseTransactions;
    use WithFaker;

    public AdminBuilder $adminBuilder;
    public UserBuilder $userBuilder;
    public function setUp(): void
    {
        parent::setUp();

        $this->langInit();

        $this->adminBuilder = resolve(AdminBuilder::class);
        $this->userBuilder = resolve(UserBuilder::class);
    }

    /** @test */
    public function success_send_admin()
    {
        $link = 'some_link';
        /** @var $model Admin */
        $model = $this->adminBuilder->create();

        $notifications = new ForgotPasswordVerificationNotification($model, $link);
        $msg = $notifications->toMail($model);

        $this->assertEquals($msg->subject, __('messages.forgot_password.subject'));
        $this->assertEquals(
            $msg->greeting,
            __('messages.forgot_password.greeting', ['name' => $model->getName()])
        );

        $this->assertEquals(
            $msg->introLines[0],
            __('mail.forgot_password.line_1')
        );
        $this->assertEquals(
            $msg->introLines[1],
            __('mail.forgot_password.line_2')
        );
        $this->assertEquals(
            $msg->introLines[2],
            new HtmlString(__('mail.forgot_password.line_3', ['link' => $link]))
        );
    }

    /** @test */
    public function success_send_user()
    {
        $link = 'some_link';
        /** @var $model User */
        $model = $this->userBuilder->create();

        $notifications = new ForgotPasswordVerificationNotification($model, $link);
        $msg = $notifications->toMail($model);

        $this->assertEquals($msg->subject, __('messages.forgot_password.subject'));
        $this->assertEquals(
            $msg->greeting,
            __('messages.forgot_password.greeting', ['name' => $model->getName()])
        );

        $this->assertEquals(
            $msg->introLines[0],
            __('mail.forgot_password.line_1')
        );
        $this->assertEquals(
            $msg->introLines[1],
            __('mail.forgot_password.line_2')
        );
        $this->assertEquals(
            $msg->introLines[2],
            new HtmlString(__('mail.forgot_password.line_3', ['link' => $link]))
        );
    }
}
