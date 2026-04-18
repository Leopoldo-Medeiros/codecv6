<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeNotification extends Notification
{
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $dashboardUrl = config('app.frontend_url', config('app.url')) . '/dashboard';

        return (new MailMessage)
            ->subject('Welcome to CODECV!')
            ->greeting("Hello, {$notifiable->fullname}!")
            ->line('Your account has been created successfully. We\'re excited to have you on board.')
            ->action('Go to Dashboard', $dashboardUrl)
            ->line('With CODECV you can analyse your CV, explore learning paths, and accelerate your IT career.')
            ->line('If you have any questions, just reply to this email — we\'re happy to help.');
    }
}
