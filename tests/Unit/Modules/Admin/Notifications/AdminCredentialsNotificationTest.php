<?php

namespace Tests\Unit\Modules\Admin\Notifications;

use App\Modules\Admin\Models\Admin;
use App\Modules\Admin\Notifications\AdminCredentialsNotification;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\HtmlString;
use Tests\Builders\Admins\AdminBuilder;
use Tests\TestCase;

class AdminCredentialsNotificationTest extends TestCase
{
    use DatabaseTransactions;

    public AdminBuilder $adminBuilder;
    public function setUp(): void
    {
        parent::setUp();

        $this->langInit();

        $this->adminBuilder = resolve(AdminBuilder::class);
    }

    /** @test */
    public function success_send()
    {
        /** @var $model Admin */
        $model = $this->adminBuilder->create();

        $notifications = new AdminCredentialsNotification($model, 'password');
        $msg = $notifications->toMail($model);

        $this->assertEquals($msg->subject, __('mail.send_credential.subject'));
        $this->assertEquals(
            $msg->greeting,
            __('mail.send_credential.greeting', [
                'name' => $model->name
            ])
        );

        $this->assertEquals(
            $msg->introLines[0], new HtmlString('<br>')
        );
        $this->assertEquals(
            $msg->introLines[1], __('mail.send_credential.body')
        );
        $this->assertEquals(
            $msg->introLines[2], new HtmlString('<br>')
        );
        $this->assertEquals(
            $msg->introLines[3],
            __('mail.send_credential.login', [
                'login' => $model->email->getValue()
            ])
        );
        $this->assertEquals(
            $msg->introLines[4], new HtmlString('<br>')
        );
        $this->assertEquals(
            $msg->introLines[5],
            __('mail.send_credential.password', [
                'password' => 'password'
            ])
        );
    }
}

