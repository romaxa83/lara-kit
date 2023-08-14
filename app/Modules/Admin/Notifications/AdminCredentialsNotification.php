<?php

namespace App\Modules\Admin\Notifications;

use App\Modules\Admin\Models\Admin;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\HtmlString;

class AdminCredentialsNotification extends Notification implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        protected Admin $model,
        protected string $password,
    )
    {}

    public function via(mixed $notifiable): array
    {
        return [
            'mail',
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject(__('mail.send_credential.subject'))
            ->greeting(__('mail.send_credential.greeting', [
                'name' => $this->model->name
            ]))
            ->line(new HtmlString('<br>'))
            ->line(__('mail.send_credential.body'))
            ->line(new HtmlString('<br>'))
            ->line(__('mail.send_credential.login', [
                'login' => $this->model->email->getValue()
            ]))
            ->line(new HtmlString('<br>'))
            ->line(__('mail.send_credential.password', [
                'password' => $this->password
            ]))
            ;
    }
}
