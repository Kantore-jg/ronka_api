<?php

namespace App\Mail;

use App\Models\Feedback;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FeedbackReceived extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Feedback $feedback
    ) {}

    public function envelope(): Envelope
    {
        $senderEmail = $this->feedback->sender_email
            ?? (filter_var($this->feedback->contact ?? '', FILTER_VALIDATE_EMAIL) ?: config('mail.from.address'));
        $envelope = new Envelope(
            subject: '[RONKA] ' . ucfirst($this->feedback->type) . ' reçu',
            replyTo: [$senderEmail],
        );
        return $envelope;
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.feedback-received'
        );
    }
}
