<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    public function __construct(private readonly string $token) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = config('app.frontend_url', config('app.url'))
            .'/reset-password?token='.$this->token
            .'&email='.urlencode($notifiable->getEmailForPasswordReset());

        return (new MailMessage)
            ->subject('Reset your CODECV password')
            ->greeting('Hello!')
            ->line('You requested a password reset for your CODECV account.')
            ->action('Reset Password', $url)
            ->line('This link expires in 60 minutes.')
            ->line('If you did not request this, no action is needed.');
    }
}
