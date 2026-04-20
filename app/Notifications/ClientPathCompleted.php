<?php

namespace App\Notifications;

use App\Models\Path;
use App\Models\User;
use Illuminate\Notifications\Notification;

class ClientPathCompleted extends Notification
{
    public function __construct(
        private readonly User $client,
        private readonly Path $path,
        private readonly int  $totalSteps,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'        => 'client_path_completed',
            'client_id'   => $this->client->id,
            'fullname'    => $this->client->fullname,
            'path_id'     => $this->path->id,
            'path_name'   => $this->path->name,
            'total_steps' => $this->totalSteps,
        ];
    }
}
