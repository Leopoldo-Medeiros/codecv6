<?php

namespace App\Notifications;

use App\Mail\WelcomeMail;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Mail;

class WelcomeNotification extends Notification
{
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): WelcomeMail
    {
        return (new WelcomeMail($notifiable))->to($notifiable->email);
    }
}
