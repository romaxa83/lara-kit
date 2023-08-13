<?php

namespace App\Notifications\Auth;

use Core\Models\BaseAuthenticatable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class ForgotPasswordVerificationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public BaseAuthenticatable $user,
        public string $link,
    ) {
    }

    public function via(mixed $notifiable): array
    {
        return [
            'mail',
        ];
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        return app(MailMessage::class)
            ->greeting(__('mail.forgot_password.greeting', ['name' => $this->user->getName()]))
            ->subject(__('mail.forgot_password.subject'))
            ->line(__('mail.forgot_password.line_1'))
            ->line(__('mail.forgot_password.line_2'))
            ->line(new HtmlString(__('mail.forgot_password.line_3', ['link' => $this->link])));
    }
}
