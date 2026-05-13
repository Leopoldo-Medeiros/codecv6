<?php

namespace App\Notifications;

use App\Models\Path;
use App\Models\User;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PathAssigned extends Notification
{
    public function __construct(
        private readonly User $consultant,
        private readonly Path $path,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $pathUrl = config('app.frontend_url').'/my-paths';

        return (new MailMessage)
            ->subject('New learning path assigned: '.$this->path->name)
            ->greeting('Hey '.$notifiable->fullname.'!')
            ->line($this->consultant->fullname.' has assigned you a new learning path: **'.$this->path->name.'**.')
            ->line('Log in to start working through your steps at your own pace.')
            ->action('View My Paths', $pathUrl);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'path_assigned',
            'path_id' => $this->path->id,
            'path_name' => $this->path->name,
            'consultant_name' => $this->consultant->fullname,
        ];
    }
}
