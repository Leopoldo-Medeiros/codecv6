<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Notifications\Notification;

class NewClientOnboarded extends Notification
{
    public function __construct(private readonly User $client) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $profile = $this->client->profile;

        return [
            'type' => 'new_client_onboarded',
            'client_id' => $this->client->id,
            'fullname' => $this->client->fullname,
            'profession' => $profile?->profession,
            'level' => $profile?->level,
            'stack' => $profile?->stack,
            'product_interest' => $profile?->product_interest,
            'availability_hours' => $profile?->availability_hours,
            'timeline' => $profile?->timeline,
            'goal' => $profile?->goal,
        ];
    }
}
