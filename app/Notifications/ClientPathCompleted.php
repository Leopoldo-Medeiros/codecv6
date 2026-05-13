<?php

namespace App\Notifications;

use App\Models\Path;
use App\Models\User;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClientPathCompleted extends Notification
{
    public function __construct(
        private readonly User $client,
        private readonly Path $path,
        private readonly int $totalSteps,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $clientUrl = config('app.frontend_url').'/my-clients/'.$this->client->id;

        return (new MailMessage)
            ->subject($this->client->fullname.' completed: '.$this->path->name)
            ->greeting('Great news, '.$notifiable->fullname.'!')
            ->line($this->client->fullname.' just completed all '.$this->totalSteps.' steps of **'.$this->path->name.'**.')
            ->line('This is a good time to schedule a progress call or assign a new path.')
            ->action('View Client', $clientUrl);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'client_path_completed',
            'client_id' => $this->client->id,
            'fullname' => $this->client->fullname,
            'path_id' => $this->path->id,
            'path_name' => $this->path->name,
            'total_steps' => $this->totalSteps,
        ];
    }
}
