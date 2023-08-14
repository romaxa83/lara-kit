<?php

namespace Tests\Unit\Modules\Admin\Listeners;

use App\Modules\Admin\Dto\AdminDto;
use App\Modules\Admin\Events\AdminCreatedEvent;
use App\Modules\Admin\Listeners\SendAdminCredentialsListener;
use App\Modules\Admin\Models\Admin;
use App\Modules\Admin\Notifications\AdminCredentialsNotification;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;
use Tests\Builders\Admins\AdminBuilder;
use Tests\TestCase;

class SendAdminCredentialsListenerTest extends TestCase
{
    use DatabaseTransactions;
    use WithFaker;

    protected AdminBuilder $adminBuilder;

    public function setUp(): void
    {
        parent::setUp();

        $this->adminBuilder = resolve(AdminBuilder::class);

        $this->langInit();
    }

    /** @test */
    public function success_send()
    {
        Notification::fake();

        /** @var $model Admin */
        $model = $this->adminBuilder->create();
        $dto = AdminDto::byArgs([
            'name' => $model->name,
            'email' => $model->email->getValue(),
            'role' => $model->role->id,
            'lang' => $model->lang,
            'password' => 'password'
        ]);

        $event = new AdminCreatedEvent($model, $dto);

        /** @var $listener SendAdminCredentialsListener */
        $listener = resolve(SendAdminCredentialsListener::class);
        $listener->handle($event);

        Notification::assertSentTo(new AnonymousNotifiable(), AdminCredentialsNotification::class,
            fn ($notification, $channels, $notifiable) =>
                $notifiable->routes['mail'] == $model->email->getValue()
        );
    }

    /** @test */
    public function not_send_no_dto()
    {
        Notification::fake();

        /** @var $model Admin */
        $model = $this->adminBuilder->create();

        $event = new AdminCreatedEvent($model);

        /** @var $listener SendAdminCredentialsListener */
        $listener = resolve(SendAdminCredentialsListener::class);
        $listener->handle($event);

        Notification::assertNotSentTo(new AnonymousNotifiable(), AdminCredentialsNotification::class,
            fn ($notification, $channels, $notifiable) =>
                $notifiable->routes['mail'] == $model->email->getValue()
        );
    }
}
