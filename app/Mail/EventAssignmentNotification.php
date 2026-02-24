<?php

namespace App\Mail;

use App\Models\Event;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EventAssignmentNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $member,
        public Event $event,
        public string $confirmUrl,
        public string $declineUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "[RONKA] Vous êtes assigné à l'événement : {$this->event->title}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.event-assignment',
        );
    }
}
