<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly User $user) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Welcome to CODECV!');
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.welcome',
            with: [
                'dashboardUrl' => config('app.frontend_url', config('app.url')).'/dashboard',
            ],
        );
    }
}
