<?php

namespace App\Notifications\Users;

use App\Modules\User\Models\User;
use App\Traits\MailNotificationChannelTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendPasswordNotification extends Notification implements ShouldQueue
{
    use Queueable;
    use MailNotificationChannelTrait;

    public function __construct(public User $user, public string $password)
    {
    }

    public function via($notifiable): array
    {
        return [
            'mail',
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject(__('passwords.email.registration_subject'))
            ->greeting(__('messages.default-greeting'))
            ->line(__('passwords.email.greeting', ['name' => $this->user->getName()]))
            ->line(__('passwords.email.registration_success'))
            ->action(__('passwords.email.login'), config('front_routes.thank-you-page'))
            ->markdown(
                'notifications::email',
                [
                    'additional_info' => [
                        __('fields.email') => $this->user->email,
                        __('fields.password') => $this->password,
                    ],
                ]
            );
    }
}
