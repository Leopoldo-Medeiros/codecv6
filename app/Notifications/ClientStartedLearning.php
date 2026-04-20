<?php

namespace App\Notifications;

use App\Models\Path;
use App\Models\User;
use Illuminate\Notifications\Notification;

class ClientStartedLearning extends Notification
{
    public function __construct(
        private readonly User $client,
        private readonly Path $path,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'       => 'client_started_learning',
            'client_id'  => $this->client->id,
            'fullname'   => $this->client->fullname,
            'path_id'    => $this->path->id,
            'path_name'  => $this->path->name,
        ];
    }
}
