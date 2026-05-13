<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClientAssigned extends Notification
{
    public function __construct(private readonly User $client) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $profile = $this->client->profile;
        $clientUrl = config('app.frontend_url').'/my-clients/'.$this->client->id;

        $mail = (new MailMessage)
            ->subject('New client assigned: '.$this->client->fullname)
            ->greeting('Hey '.$notifiable->fullname.'!')
            ->line($this->client->fullname.' has been assigned to you.');

        if ($profile?->profession) {
            $mail->line('**Profession:** '.$profile->profession);
        }

        return $mail->action('View Client', $clientUrl);
    }

    public function toArray(object $notifiable): array
    {
        $profile = $this->client->profile;

        return [
            'type' => 'client_assigned',
            'client_id' => $this->client->id,
            'fullname' => $this->client->fullname,
            'profession' => $profile?->profession,
            'level' => $profile?->level,
        ];
    }
}
