<?php

namespace Tests\Unit\Notifications\Auth;

use App\Modules\Admin\Models\Admin;
use App\Modules\User\Models\User;
use App\Notifications\Auth\ResetPasswordVerificationNotification;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\HtmlString;
use Tests\Builders\Admins\AdminBuilder;
use Tests\Builders\Users\UserBuilder;
use Tests\TestCase;

class ResetPasswordVerificationNotificationTest extends TestCase
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
    public function success_send_as_admin()
    {
        $password = 'password';
        /** @var $model Admin */
        $model = $this->adminBuilder->password($password)->create();

        $notifications = new ResetPasswordVerificationNotification($model, $password);
        $msg = $notifications->toMail($model);

        $this->assertEquals($msg->subject, __('messages.reset_password.subject'));
        $this->assertEquals(
            $msg->greeting,
            __('messages.reset_password.greeting', ['name' => $model->getName()])
        );

        $this->assertEquals(
            $msg->introLines[0],
            new HtmlString(__('messages.reset_password.line_1', ['password' => $password]))
        );
        $this->assertEquals(
            $msg->introLines[1],
            __('messages.reset_password.line_2')
        );
    }

    /** @test */
    public function success_send_as_user()
    {
        $password = 'password';
        /** @var $model User */
        $model = $this->userBuilder->password($password)->create();

        $notifications = new ResetPasswordVerificationNotification($model, $password);
        $msg = $notifications->toMail($model);

        $this->assertEquals($msg->subject, __('messages.reset_password.subject'));
        $this->assertEquals(
            $msg->greeting,
            __('messages.reset_password.greeting', ['name' => $model->getName()])
        );

        $this->assertEquals(
            $msg->introLines[0],
            new HtmlString(__('messages.reset_password.line_1', ['password' => $password]))
        );
        $this->assertEquals(
            $msg->introLines[1],
            __('messages.reset_password.line_2')
        );
    }
}
