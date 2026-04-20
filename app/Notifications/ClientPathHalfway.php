<?php

namespace App\Notifications;

use App\Models\Path;
use App\Models\User;
use Illuminate\Notifications\Notification;

class ClientPathHalfway extends Notification
{
    public function __construct(
        private readonly User $client,
        private readonly Path $path,
        private readonly int  $doneCount,
        private readonly int  $totalCount,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'        => 'client_path_halfway',
            'client_id'   => $this->client->id,
            'fullname'    => $this->client->fullname,
            'path_id'     => $this->path->id,
            'path_name'   => $this->path->name,
            'done_count'  => $this->doneCount,
            'total_count' => $this->totalCount,
        ];
    }
}
