<?php

namespace App\Notifications\Users;

use App\Modules\User\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserEmailVerification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public User $user)
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
            ->greeting(__('verification.email.greeting', ['name' => $this->user->getName()]))
            ->subject(__('verification.email.subject'))
            ->line(__('verification.email.code', ['code' => $this->user->getEmailVerificationCode()]));
    }
}
